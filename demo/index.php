<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\EflClient;
use Imoli\EflLeasingSdk\Exception\ApiException;
use Imoli\EflLeasingSdk\Exception\EflLeasingException;
use Imoli\EflLeasingSdk\Http\Adapter\GuzzleHttpAdapter;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use Imoli\EflLeasingSdk\Model\Customer\Phone;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;

session_start();

/**
 * Captures the last API response for debug output.
 */
final class DemoResponseCapture implements \Imoli\EflLeasingSdk\Http\RequestLoggerInterface
{
    public ?int $lastStatusCode = null;

    public ?string $lastBody = null;

    public function logRequest(string $method, string $url, array $headers, ?string $body): void
    {
    }

    public function logResponse(int $statusCode, string $body): void
    {
        $this->lastStatusCode = $statusCode;
        $this->lastBody = $body;
    }
}

// Simple .env loader
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '' && str_starts_with($line, 'EFL_') && str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if ($name !== '') {
                putenv($name . '=' . $value);
            }
        }
    }
}

function extractTransactionId(string $initResult): ?string
{
    $trimmed = trim($initResult);
    if ($trimmed === '') {
        return null;
    }
    if (str_starts_with($trimmed, '{')) {
        $data = json_decode($trimmed, true);
        if (is_array($data)) {
            $txId = $data['transactionId'] ?? $data['TransactionId'] ?? $data['transaction_id'] ?? null;
            if ($txId !== null && $txId !== '') {
                return (string) $txId;
            }
            $url = $data['url'] ?? $data['Url'] ?? $data['redirectUrl'] ?? null;
            if ($url !== null && $url !== '') {
                $fromUrl = extractTransactionIdFromUrl((string) $url);
                if ($fromUrl !== null) {
                    return $fromUrl;
                }
            }
        }
    }
    $fromUrl = extractTransactionIdFromUrl($trimmed);
    if ($fromUrl !== null) {
        return $fromUrl;
    }
    if (str_contains($trimmed, '://') || str_contains($trimmed, '?')) {
        return null;
    }
    if (preg_match('/^[a-zA-Z0-9\-_]{10,}$/', $trimmed)) {
        return $trimmed;
    }
    return null;
}

function extractTransactionIdFromUrl(string $url): ?string
{
    $parsed = parse_url($url);
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $params);
        $value = $params['transactionId'] ?? $params['TransactionId'] ?? $params['transaction_id'] ?? null;
        if ($value !== null) {
            return is_array($value) ? (string) ($value[0] ?? '') : (string) $value;
        }
    }
    $path = $parsed['path'] ?? '';
    if (preg_match('/[a-fA-F0-9\-]{20,}/', $path, $m)) {
        return $m[0];
    }
    return null;
}

/**
 * Ensures a query parameter exists in the URL (adds or overwrites if empty).
 * Used to patch redirect URLs from API that may lack the token.
 */
function ensureQueryParam(string $url, string $key, string $value): string
{
    $parts = parse_url($url);
    if (!is_array($parts)) {
        return $url;
    }
    $query = [];
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    if (!isset($query[$key]) || (string) $query[$key] === '') {
        $query[$key] = $value;
    }
    $scheme = $parts['scheme'] ?? 'https';
    $host = $parts['host'] ?? '';
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    $path = $parts['path'] ?? '';
    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
    $newQuery = http_build_query($query);

    return $scheme . '://' . $host . $port . $path . ($newQuery !== '' ? '?' . $newQuery : '') . $fragment;
}

function renderPage(string $title, string $content, bool $wide = false): void
{
    $maxWidth = $wide ? '900px' : '600px';
    echo '<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . htmlspecialchars($title) . '</title>';
    echo '<style>*,*::before,*::after{box-sizing:border-box;}';
    echo 'body{font-family:system-ui,-apple-system,sans-serif;max-width:' . $maxWidth . ';margin:0 auto;padding:1rem;line-height:1.5;}';
    echo 'h1{font-size:1.5rem;margin:0 0 0.5rem;}h2{font-size:1.1rem;margin:0 0 1rem;}';
    echo 'fieldset{border:1px solid #ccc;margin-bottom:1.5rem;padding:1rem;min-width:0;}';
    echo 'legend{font-weight:bold;padding:0 0.5rem;}';
    echo 'label{display:block;margin-top:0.75rem;}label:first-child{margin-top:0;}';
    echo 'input,button,select{width:100%;padding:0.5rem;font-size:1rem;box-sizing:border-box;min-height:2.5rem;}';
    echo 'button{margin-top:1rem;cursor:pointer;min-height:2.75rem;}';
    echo 'button.secondary{padding:0.5rem 1rem;width:auto;min-height:auto;}';
    echo 'button.btn-remove:disabled{opacity:0.5;cursor:not-allowed;}';
    echo '.error{color:#c00;}.section{margin-top:1.5rem;}';
    echo '.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}';
    echo '.grid2 label{margin-top:0.75rem;}';
    echo '.product-block{border:1px solid #ddd;padding:1rem;margin-bottom:1rem;background:#fafafa;border-radius:4px;}';
    echo '.product-block h3{margin:0 0 0.75rem;font-size:1rem;}';
    echo '.product-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;gap:0.5rem;}';
    echo '.path-options{display:flex;flex-direction:column;gap:0.5rem;margin-top:1rem;}';
    echo '.path-option{display:block;padding:1rem;border:2px solid #ccc;border-radius:6px;cursor:pointer;text-align:left;}';
    echo '.path-option:hover{border-color:#0066cc;background:#f8f9fa;}';
    echo '.path-option input{width:auto;margin-right:0.5rem;}';
    echo 'input[type=radio]{width:auto;min-height:auto;}input[type=checkbox]{width:auto;min-height:auto;}';
    echo '@media (max-width:640px){body{padding:0.75rem;}.grid2{grid-template-columns:1fr;}}';
    echo '</style></head><body><h1>' . htmlspecialchars($title) . '</h1>' . $content . '</body></html>';
}

function hiddenFields(array $data, string $prefix = ''): string
{
    $html = '';
    foreach ($data as $k => $v) {
        $name = $prefix !== '' ? $prefix . '[' . $k . ']' : $k;
        if (is_array($v)) {
            if (array_keys($v) !== range(0, count($v) - 1) || count($v) === 0) {
                $html .= hiddenFields($v, $name);
            } else {
                foreach ($v as $i => $val) {
                    if (is_array($val)) {
                        $html .= hiddenFields($val, $name . '[' . $i . ']');
                    } else {
                        $html .= '<input type="hidden" name="' . htmlspecialchars($name . '[' . $i . ']') . '" value="' . htmlspecialchars((string) $val) . '">';
                    }
                }
            }
        } else {
            $html .= '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars((string) $v) . '">';
        }
    }
    return $html;
}

/**
 * @param \Imoli\EflLeasingSdk\Model\Calculation\OfferVariant[] $variants
 */
function variantSlidersHtml(array $variants): string
{
    $durations = [];
    $payments = [];
    $variantMap = [];
    foreach ($variants as $i => $v) {
        $d = $v->duration ?? 0;
        $p = $v->payment ?? 0;
        $durations[$d] = true;
        $payments[$p] = true;
        $variantMap[$d . '_' . $p] = $i;
    }
    $durations = array_keys($durations);
    $payments = array_keys($payments);
    sort($durations);
    sort($payments);
    if ($durations === []) {
        $durations = [12];
    }
    if ($payments === []) {
        $payments = [0];
    }
    $variantData = [];
    foreach ($variants as $i => $v) {
        $t = $v->total;
        $dur = $v->duration ?? 0;
        $pure = $t?->pure;
        $netTotal = $pure?->netTotalAmount;
        $grossTotal = $pure?->grossTotalAmount;
        $kwotaWykupuNetto = $t !== null ? ($t->netResidualValue ?? $t->netLastRentResidualValue) : null;
        $kwotaWykupuBrutto = $t?->grossResidualValue;
        if ($netTotal === null && $t?->netOfferValue !== null && $kwotaWykupuNetto !== null) {
            $wplataNetto = $t->netInitialPayment ?? 0;
            $netTotal = round($t->netOfferValue - $wplataNetto - $kwotaWykupuNetto, 2);
        } elseif ($netTotal === null && $pure?->netInstallmentAmount !== null && $dur > 0) {
            $netTotal = round($pure->netInstallmentAmount * $dur + ($pure->netResidualInstallmentAmount ?? 0), 2);
        }
        if ($grossTotal === null && $t?->grossOfferValue !== null && ($kwotaWykupuBrutto !== null || $kwotaWykupuNetto !== null)) {
            $wplataBrutto = $t->grossInitialPayment ?? 0;
            $kwotaBrutto = $kwotaWykupuBrutto ?? ($kwotaWykupuNetto !== null ? round($kwotaWykupuNetto * 1.23, 2) : 0);
            $grossTotal = round($t->grossOfferValue - $wplataBrutto - $kwotaBrutto, 2);
        } elseif ($grossTotal === null && $pure?->grossInstallmentAmount !== null && $dur > 0) {
            $grossTotal = round($pure->grossInstallmentAmount * $dur, 2);
        }
        $kwotaWykupuBruttoVal = $t !== null ? ($t->grossResidualValue ?? ($kwotaWykupuNetto !== null && $kwotaWykupuNetto > 0 ? round($kwotaWykupuNetto * 1.23, 2) : null)) : null;
        $procentWykupu = $t !== null ? ($t->grossResidualValuePercent ?? $t->netResidualValuePercent) : null;
        if ($procentWykupu === null && $kwotaWykupuNetto !== null && $t?->netOfferValue !== null && $t->netOfferValue > 0) {
            $procentWykupu = round($kwotaWykupuNetto / $t->netOfferValue * 100, 1);
        }
        $ins = $t?->insurance;
        $variantData[$i] = [
            'cenaBrutto' => $t !== null ? $t->grossOfferValue : null,
            'cenaNetto' => $t !== null ? $t->netOfferValue : null,
            'recommendedPrice' => $t !== null ? $t->recommendedPrice : null,
            'partnerGrossOfferValue' => $t !== null ? $t->partnerGrossOfferValue : null,
            'wplataWlasnaBrutto' => $t !== null ? $t->grossInitialPayment : null,
            'wplataWlasnaNetto' => $t !== null ? $t->netInitialPayment : null,
            'rataBrutto' => $t !== null ? $t->calculatedGrossInstallmentValue : null,
            'rataNetto' => $t !== null ? $t->calculatedNetInstallmentValue : null,
            'pureNetInstallment' => $pure?->netInstallmentAmount,
            'pureVatInstallment' => $pure?->vatInstallmentAmount,
            'pureGrossInstallment' => $pure?->grossInstallmentAmount,
            'pureNetResidualInstallment' => $pure?->netResidualInstallmentAmount,
            'pureNetTotal' => $netTotal,
            'pureGrossTotal' => $grossTotal,
            'kwotaWykupuBrutto' => $kwotaWykupuBruttoVal,
            'kwotaWykupuNetto' => $kwotaWykupuNetto,
            'procentWykupu' => $procentWykupu,
            'netResidualValuePercent' => $t !== null ? $t->netResidualValuePercent : null,
            'grossResidualValuePercent' => $t !== null ? $t->grossResidualValuePercent : null,
            'netInitialResidualValue' => $t !== null ? $t->netInitialResidualValue : null,
            'netLastRentResidualValue' => $t !== null ? $t->netLastRentResidualValue : null,
            'insuranceNetInstallment' => $ins?->netInsuranceInstallmentAmount,
            'insuranceVatInstallment' => $ins?->vatInsuranceInstallmentAmount,
            'insuranceGrossInstallment' => $ins?->grossInsuranceInstallmentAmount,
            'insuranceNetTotal' => $ins?->netInsuranceTotalAmount,
            'insuranceGrossTotal' => $ins?->grossInsuranceTotalAmount,
        ];
    }
    $firstDur = $durations[0] ?? 12;
    $firstPay = $payments[0] ?? 0;
    $firstKey = $firstDur . '_' . $firstPay;
    $firstIdx = $variantMap[$firstKey] ?? 0;
    $durMax = max(0, count($durations) - 1);
    $payMax = max(0, count($payments) - 1);

    $summaryFields = [
        ['cenaBrutto', 'Cena oferty brutto', 'pln'],
        ['cenaNetto', 'Cena oferty netto', 'pln'],
        ['recommendedPrice', 'Cena rekomendowana', 'pln'],
        ['partnerGrossOfferValue', 'Wartość oferty brutto (partner)', 'pln'],
        ['wplataWlasnaBrutto', 'Wpłata własna brutto', 'pln'],
        ['wplataWlasnaNetto', 'Wpłata własna netto', 'pln'],
        ['rataBrutto', 'Rata miesięczna brutto', 'pln'],
        ['rataNetto', 'Rata miesięczna netto', 'pln'],
        ['pureNetInstallment', 'Rata netto (czysta)', 'pln'],
        ['pureVatInstallment', 'VAT w racie', 'pln'],
        ['pureGrossInstallment', 'Rata brutto (czysta)', 'pln'],
        ['pureNetResidualInstallment', 'Rata rezydualna netto', 'pln'],
        ['pureNetTotal', 'Suma rat netto', 'pln'],
        ['pureGrossTotal', 'Suma rat brutto', 'pln'],
        ['kwotaWykupuBrutto', 'Kwota wykupu brutto', 'pln'],
        ['kwotaWykupuNetto', 'Kwota wykupu netto', 'pln'],
        ['procentWykupu', 'Procent wykupu', 'pct'],
        ['netResidualValuePercent', 'Procent wykupu netto (API)', 'pct'],
        ['grossResidualValuePercent', 'Procent wykupu brutto (API)', 'pct'],
        ['netInitialResidualValue', 'Wartość rezydualna netto (początkowa)', 'pln'],
        ['netLastRentResidualValue', 'Wartość rezydualna netto (ostatnia rata)', 'pln'],
        ['insuranceNetInstallment', 'Ubezpieczenie – rata netto', 'pln'],
        ['insuranceVatInstallment', 'Ubezpieczenie – VAT w racie', 'pln'],
        ['insuranceGrossInstallment', 'Ubezpieczenie – rata brutto', 'pln'],
        ['insuranceNetTotal', 'Ubezpieczenie – suma netto', 'pln'],
        ['insuranceGrossTotal', 'Ubezpieczenie – suma brutto', 'pln'],
    ];

    $sections = [
        'Cena oferty' => ['cenaBrutto', 'cenaNetto', 'recommendedPrice', 'partnerGrossOfferValue'],
        'Wpłata własna' => ['wplataWlasnaBrutto', 'wplataWlasnaNetto'],
        'Rata miesięczna' => ['rataBrutto', 'rataNetto', 'pureNetInstallment', 'pureVatInstallment', 'pureGrossInstallment', 'pureNetResidualInstallment'],
        'Suma rat' => ['pureNetTotal', 'pureGrossTotal'],
        'Kwota wykupu' => ['kwotaWykupuBrutto', 'kwotaWykupuNetto', 'procentWykupu', 'netResidualValuePercent', 'grossResidualValuePercent', 'netInitialResidualValue', 'netLastRentResidualValue'],
        'Ubezpieczenie' => ['insuranceNetInstallment', 'insuranceVatInstallment', 'insuranceGrossInstallment', 'insuranceNetTotal', 'insuranceGrossTotal'],
    ];
    $fieldMap = [];
    foreach ($summaryFields as [$key, $label, $suffix]) {
        $fieldMap[$key] = [$label, $suffix];
    }

    $html = '<div class="variant-sliders" style="background:#f8f9fa;padding:1.25rem;border-radius:8px;margin:1rem 0;">';
    $html .= '<label>Okres finansowania: <strong id="slider-duration-value">' . $firstDur . '</strong> miesięcy</label>';
    $html .= '<input type="range" id="slider-duration" min="0" max="' . $durMax . '" value="0" step="1" style="width:100%;">';
    $html .= '<label>Wpłata własna: <strong id="slider-payment-value">' . $firstPay . '</strong>%</label>';
    $html .= '<input type="range" id="slider-payment" min="0" max="' . $payMax . '" value="0" step="1" style="width:100%;">';
    $html .= '<div id="variant-summary" style="margin-top:1rem;padding:1rem;background:#fff;border:1px solid #dee2e6;border-radius:4px;">';
    $firstSection = true;
    foreach ($sections as $sectionTitle => $keys) {
        $sectionStyle = $firstSection ? 'margin-top:0;' : 'margin-top:0.75rem;border-top:1px solid #eee;padding-top:0.5rem;';
        $firstSection = false;
        $html .= '<div style="' . $sectionStyle . '">';
        $html .= '<p style="margin:0 0 0.35rem 0;font-weight:bold;font-size:0.9em;color:#333;">' . htmlspecialchars($sectionTitle) . '</p>';
        foreach ($keys as $key) {
            [$label, $suffix] = $fieldMap[$key] ?? [$key, 'pln'];
            $unit = $suffix === 'pct' ? '' : ' PLN';
            $html .= '<p style="margin:0.25rem 0;padding-left:0.5rem;font-size:0.95em;"><strong>' . htmlspecialchars($label) . ':</strong> <span id="val-' . htmlspecialchars($key) . '">–</span>' . $unit . '</p>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '<input type="hidden" name="variant_index" id="variant-index-input" value="' . $firstIdx . '">';
    $html .= '</div>';
    $html .= '<script>
    (function(){var durations=' . json_encode($durations) . ',payments=' . json_encode($payments) . ',map=' . json_encode($variantMap) . ',data=' . json_encode($variantData) . ';
    var dur=document.getElementById("slider-duration"),pay=document.getElementById("slider-payment"),idxInp=document.getElementById("variant-index-input");
    function fmt(v){return v!=null?Number(v).toFixed(2):"–";}
    function pct(v){return v!=null?Number(v).toFixed(1)+"%":"–";}
    function update(){var di=parseInt(dur.value,10),pi=parseInt(pay.value,10);
    var d=durations[di]||durations[0],p=payments[pi]||payments[0];
    document.getElementById("slider-duration-value").textContent=d;
    document.getElementById("slider-payment-value").textContent=p;
    var key=d+"_"+p,i=map[key];if(i===undefined){for(var k in map){i=map[k];break;}i=i||0;}
    idxInp.value=i;var v=data[i];
    if(v){var pctKeys=["procentWykupu","netResidualValuePercent","grossResidualValuePercent"];
    for(var k in v){var el=document.getElementById("val-"+k);if(el)el.textContent=pctKeys.indexOf(k)>=0?pct(v[k]):fmt(v[k]);}}
    }
    dur.oninput=pay.oninput=update;update();})();
    </script>';
    return $html;
}

function productRowHtml(int $index, array $values = []): string
{
    $v = fn (string $k, string $d) => htmlspecialchars((string) ($values[$k] ?? $d));
    $name = fn (string $f) => 'products[' . $index . '][' . $f . ']';
    return '<div class="product-block">
        <div class="product-header"><h3>Produkt ' . ($index + 1) . '</h3><button type="button" class="secondary btn-remove">Usuń</button></div>
        <div class="grid2"><label>Nazwa <input type="text" name="' . $name('product_name') . '" value="' . $v('product_name', 'Laptop') . '" required></label>
        <label>ID produktu <input type="text" name="' . $name('product_id') . '" value="' . $v('product_id', 'demo-1') . '"></label></div>
        <div class="grid2"><label>Ilość <input type="number" name="' . $name('quantity') . '" value="' . $v('quantity', '1') . '" min="1" required></label>
        <label>Cena netto (PLN) <input type="number" name="' . $name('price_net') . '" value="' . $v('price_net', '5000') . '" min="0" step="0.01" required></label></div>
        <div class="grid2"><label>VAT (%) <input type="number" name="' . $name('vat_rate') . '" value="' . $v('vat_rate', '23') . '" min="0" max="100"></label>
        <label>ID dostawcy <input type="text" name="' . $name('supplier_id') . '" value="' . $v('supplier_id', '8951628481') . '"></label></div>
        <div class="grid2"><label>Marka <input type="text" name="' . $name('product_make') . '" value="' . $v('product_make', 'Samsung') . '"></label>
        <label>Model <input type="text" name="' . $name('product_model') . '" value="' . $v('product_model', 'Galaxy S21FE') . '"></label></div>
        <div class="grid2"><label>Klasa <input type="text" name="' . $name('product_class') . '" value="' . $v('product_class', '1') . '"></label>
        <label>Rok produkcji <input type="text" name="' . $name('production_year') . '" value="' . $v('production_year', '2024') . '"></label></div>
        <label>Opis zasobu 1 <input type="text" name="' . $name('asset_desc_1') . '" value="' . $v('asset_desc_1', 'Smartfon') . '"></label>
        <label>Opis <input type="text" name="' . $name('product_description') . '" value="' . $v('product_description', '') . '"></label>
    </div>';
}

function buildBasketFromPost(array $productsData, string $transactionId, ?string $returnToBasketUrl, ?bool $basketCalculation): AssetToCalculation
{
    $basketBuilder = AssetToCalculation::builder($transactionId);
    if ($returnToBasketUrl !== null) {
        $basketBuilder = $basketBuilder->withReturnToBasketUrl($returnToBasketUrl);
    }
    if ($basketCalculation !== null) {
        $basketBuilder = $basketBuilder->withBasketCalculation($basketCalculation);
    }

    foreach ($productsData as $idx => $p) {
        if (!is_array($p)) {
            continue;
        }
        $productName = trim((string) ($p['product_name'] ?? ''));
        if ($productName === '') {
            continue;
        }
        $productId = trim((string) ($p['product_id'] ?? ''));
        $supplierId = trim((string) ($p['supplier_id'] ?? '')) ?: null;
        $quantity = max(1, (int) ($p['quantity'] ?? 1));
        $priceNet = max(0.0, (float) ($p['price_net'] ?? 0));
        $vatRate = max(0.0, min(100.0, (float) ($p['vat_rate'] ?? 23)));
        $make = trim((string) ($p['product_make'] ?? ''));
        $model = trim((string) ($p['product_model'] ?? ''));
        $productClass = trim((string) ($p['product_class'] ?? ''));
        $productionYear = trim((string) ($p['production_year'] ?? ''));
        $description = trim((string) ($p['product_description'] ?? ''));
        $assetDesc1 = trim((string) ($p['asset_desc_1'] ?? ''));
        if ($description === '') {
            $description = $productName;
        }

        $itemDetails = [new ItemDetail('description', $description)];
        if ($make !== '') {
            $itemDetails[] = new ItemDetail('make', $make);
        }
        if ($model !== '') {
            $itemDetails[] = new ItemDetail('model', $model);
        }
        if ($productClass !== '') {
            $itemDetails[] = new ItemDetail('class', $productClass);
        }
        if ($productionYear !== '') {
            $itemDetails[] = new ItemDetail('productionYear', $productionYear);
        }
        if ($assetDesc1 !== '') {
            $itemDetails[] = new ItemDetail('assetDesc1', $assetDesc1);
        }

        $totalAmountNet = round($quantity * $priceNet, 4);
        if ($totalAmountNet < 1000) {
            continue;
        }
        $priceNetRounded = round($priceNet, 4);
        $item = new OfferItem(
            $quantity,
            $productId ?: 'demo-' . uniqid(),
            $vatRate,
            $itemDetails,
            $supplierId,
            null,
            null,
            $totalAmountNet,
            $priceNetRounded,
            null,
        );
        $basketBuilder = $basketBuilder->addOfferItem($item);
    }
    return $basketBuilder->build();
}

// EFL sandbox frontend base (productsSearch, leaseOffer, popup.js)
$eflFrontendBase = trim((string) (getenv('EFL_SANDBOX_FRONTEND_URL') ?: 'https://leasingonline-sandbox.efl.com.pl'));

$docsBaseUrl = rtrim(trim((string) (getenv('EFL_DOCS_BASE_URL') ?: 'https://efl-leasing.imoli.pl/sdk')), '/');

// Routing – base URL for return URLs (sandbox may reject localhost; use EFL_BASE_URL with ngrok)
$envBaseUrl = trim((string) (getenv('EFL_BASE_URL') ?: ''));
$baseUrl = $envBaseUrl !== ''
    ? rtrim($envBaseUrl, '/') . str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''))
    : (($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')));
$defaultPositiveUrl = rtrim($baseUrl, '/') . '/?success=1';
$defaultNegativeUrl = rtrim($baseUrl, '/') . '/?cancel=1';

if (isset($_GET['success'])) {
    renderPage('Demo EFL Leasing', '<p>Proces zakończony pomyślnie. Zostałeś przekierowany z powrotem z EFL.</p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót do formularza</a></p>');
    exit;
}

if (isset($_GET['cancel'])) {
    renderPage('Demo EFL Leasing', '<p>Proces został anulowany.</p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót do formularza</a></p>');
    exit;
}

$path = $_POST['path'] ?? $_GET['path'] ?? 'full';
if (!in_array($path, ['full', 'basket', 'calculate', 'simple_productsSearch', 'popup'], true)) {
    $path = 'full';
}
if ($path === 'simple_productsSearch') {
    $path = 'simple';
}
$pathForForm = $path === 'simple' ? 'simple_productsSearch' : $path;

$apiKey = getenv('EFL_API_KEY') ?: '';
$partnerId = getenv('EFL_PARTNER_ID') ?: '';
if ($apiKey === '' || $partnerId === '') {
    renderPage('Demo EFL Leasing', '<p class="error">Brak EFL_API_KEY lub EFL_PARTNER_ID. Skopiuj .env.example do .env i skonfiguruj dane dostępowe.</p>');
    exit;
}

// POST handler – step-by-step based on path
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debugCapture = (getenv('EFL_DEBUG') || isset($_GET['debug'])) ? new DemoResponseCapture() : null;
    $positiveUrl = trim((string) ($_POST['custom_positive_url'] ?? '')) ?: $defaultPositiveUrl;
    $negativeUrl = trim((string) ($_POST['custom_negative_url'] ?? '')) ?: $defaultNegativeUrl;
    $returnToBasketUrl = rtrim($baseUrl, '/');
    $basketCalculation = $path === 'basket';
    $fromCalculate = isset($_POST['from_calculate']) && $_POST['from_calculate'] === '1';
    $debugRedirect = isset($_GET['debug_redirect']) || isset($_POST['debug_redirect']);

    try {
        $config = Config::sandbox($apiKey);
        $guzzle = new GuzzleClient(['timeout' => 30, 'connect_timeout' => 10]);
        $httpClient = new GuzzleHttpAdapter($guzzle);
        $client = new EflClient($config, $httpClient, $debugCapture);

        if ($path === 'simple') {
            $token = $client->getAuthToken($partnerId);
            $initResult = $client->startProcess($positiveUrl, $negativeUrl, $token);
            $redirectUrl = null;
            $redirectSource = '';
            if (str_starts_with(trim($initResult), '{')) {
                $decoded = json_decode($initResult, true);
                if (is_array($decoded)) {
                    $redirectUrl = $decoded['url'] ?? $decoded['RedirectUrl'] ?? $decoded['redirectUrl'] ?? null;
                    if ($redirectUrl !== null) {
                        $redirectSource = 'API (JSON url)';
                    }
                }
            }
            if ($redirectUrl === null && str_starts_with(trim($initResult), 'http')) {
                $redirectUrl = trim($initResult);
                $redirectSource = 'API (plain URL)';
            }
            if ($redirectUrl === null || $redirectUrl === '') {
                $transactionId = extractTransactionId($initResult);
                if ($transactionId === null || $transactionId === '') {
                    renderPage('Demo EFL Leasing', '<p class="error">Nie udało się pobrać URL ani transactionId. Odpowiedź: <code>' . htmlspecialchars(mb_substr($initResult, 0, 300)) . '</code></p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>');
                    exit;
                }
                $baseRedirect = rtrim($eflFrontendBase, '/') . '/productsSearch';
                $redirectUrl = $baseRedirect . '?transactionId=' . urlencode($transactionId) . '&token=' . urlencode($token);
                $redirectSource = 'zbudowany z transactionId';
            }
            if ($redirectUrl !== null && $redirectUrl !== '' && strpos($redirectUrl, 'token=') === false) {
                $redirectUrl = ensureQueryParam($redirectUrl, 'token', $token);
            }
            if ($debugRedirect) {
                renderPage('Demo EFL Leasing – Debug redirect', '<h2>Simple flow – URL przekierowania</h2><p>Ścieżka „Simple” – jak <a href="https://github.com/imoli-pl/efl-leasing-wordpress-plugin">plugin WordPress</a>: init → przekierowanie.</p><p><strong>Źródło URL:</strong> ' . htmlspecialchars($redirectSource) . '</p><p><strong>URL:</strong> <a href="' . htmlspecialchars($redirectUrl) . '" target="_blank">' . htmlspecialchars($redirectUrl) . '</a></p><p><a href="' . htmlspecialchars($redirectUrl) . '">Kliknij aby przejść do EFL</a></p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót do formularza</a></p>');
                exit;
            }
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($fromCalculate && isset($_SESSION['efl_from_calculate'])) {
            $calcData = $_SESSION['efl_from_calculate'];
            $token = $calcData['token'];
            $transactionId = $calcData['transactionId'];
            $offer = $calcData['offer'];
            $productsData = $calcData['productsData'];
            unset($_SESSION['efl_from_calculate']);
        } else {
            $token = $client->getAuthToken($partnerId);
            $initResult = $client->startProcess($positiveUrl, $negativeUrl, $token);
            $transactionId = extractTransactionId($initResult);

            if ($transactionId === null || $transactionId === '') {
                renderPage('Demo EFL Leasing', '<p class="error">Nie udało się pobrać identyfikatora transakcji.</p>');
                exit;
            }

            $nip = preg_replace('/\D/', '', (string) ($_POST['nip'] ?? ''));
            if ($nip !== '') {
                $client->setProcessTypeForCompany($transactionId, $token, $nip, $basketCalculation ? true : null);
            }

            $productsData = $_POST['products'] ?? [];
            if (!is_array($productsData)) {
                $productsData = [];
            }

            $basket = buildBasketFromPost($productsData, $transactionId, $returnToBasketUrl, $basketCalculation ? true : null);

            if ($basket->getOfferItems() === []) {
                renderPage('Demo EFL Leasing', '<p class="error">Dodaj co najmniej jeden produkt z nazwą i ceną netto min. 1000 PLN.</p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>');
                exit;
            }

            $offer = $client->calculateBasicOffer($basket, $token);
        }

        if ($offer->variants === []) {
            $msg = '<p class="error">Brak wariantów oferty. Sprawdź dane produktu (min. 1000 PLN netto, poprawny NIP).</p>';
            if ($debugCapture !== null && $debugCapture->lastBody !== null) {
                $msg .= '<pre style="background:#f5f5f5;padding:1rem;font-size:12px;">' . htmlspecialchars(mb_substr($debugCapture->lastBody, 0, 1500)) . '</pre>';
            }
            renderPage('Demo EFL Leasing', $msg . '<p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>');
            exit;
        }

        // Path: basket – redirect to EFL (doc 2.2: leaseOffer?transactionId=X&token=Y)
        if ($path === 'basket') {
            $baseData = $client->getBaseData($transactionId, $token);
            $apiUrl = $baseData->signProcessRedirectUrl ?? null;
            if ($apiUrl !== null && $apiUrl !== '' && (str_starts_with($apiUrl, 'http://') || str_starts_with($apiUrl, 'https://'))) {
                $redirectUrl = ensureQueryParam($apiUrl, 'token', $token);
            } else {
                $redirectUrl = rtrim($eflFrontendBase, '/') . '/leaseOffer?transactionId=' . urlencode($transactionId) . '&token=' . urlencode($token);
            }
            header('Location: ' . $redirectUrl);
            exit;
        }

        // Path: popup – init + calculate, then show page with EFL popup (doc 2.1)
        if ($path === 'popup') {
            $popupScriptUrl = rtrim($eflFrontendBase, '/') . '/popup/popup.js';
            $popupExamplesUrl = rtrim($eflFrontendBase, '/') . '/popup/popup.html';
            $leaseOfferWindowBase = rtrim($eflFrontendBase, '/') . '/leaseOfferWindow';
            $content = '<h2>EFL Leasing – Pop-up</h2>';
            $content .= '<p>Token i transactionId są gotowe.</p>';
            $content .= '<fieldset><legend>Tryb popupu</legend>';
            $content .= '<label><input type="radio" name="popup_mode" value="redirect" checked> przekierowanie – możliwość przejścia do strony leasingu</label><br>';
            $content .= '<label><input type="radio" name="popup_mode" value="close"> zamknięcie – tylko kalkulacja</label></fieldset>';
            $content .= '<fieldset><legend>Sposób osadzenia</legend>';
            $content .= '<label><input type="radio" name="embed_type" value="popup" checked> popup – otwiera się po kliknięciu przycisku</label><br>';
            $content .= '<label><input type="radio" name="embed_type" value="inline"> bezpośrednio w stronie – treść zamiast przycisku</label></fieldset>';
            $content .= '<div id="efl-popup-wrap"><p><button type="button" id="efl-popup-btn">Otwórz pop-up EFL</button></p></div>';
            $content .= '<div id="efl-inline-wrap" style="display:none;"><iframe id="efl-inline-iframe" style="width:100%;height:80vh;min-height:400px;border:1px solid #ccc;"></iframe></div>';
            $content .= '<p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót do formularza</a></p>';
            $content .= '<style>#eflLonPopupDiv{align-items:center;justify-content:center;background:rgba(0,0,0,.5)}#eflLonPopupDiv iframe{width:90vw;height:90vh;max-width:900px;border:0}</style>';
            $content .= '<div id="eflLonPopupDiv" style="display:none;position:fixed;inset:0;z-index:9999;"></div>';
            $content .= '<script src="' . htmlspecialchars($popupScriptUrl) . '"></script>';
            $content .= '<script>'
                . '(function(){'
                . 'var token=' . json_encode($token) . ',txId=' . json_encode($transactionId) . ',base=' . json_encode($leaseOfferWindowBase) . ';'
                . '_g_eflLon_token=token;_g_eflLon_transactionId=txId;'
                . 'function iframeSrc(){return base+"?transactionId="+encodeURIComponent(txId)+"&token="+encodeURIComponent(token)+"&flowMode="+encodeURIComponent(document.querySelector("input[name=popup_mode]:checked").value);}'
                . 'function updateEmbed(){'
                . 'var e=document.querySelector("input[name=embed_type]:checked").value;'
                . 'document.getElementById("efl-popup-wrap").style.display=e==="popup"?"":"none";'
                . 'var iw=document.getElementById("efl-inline-wrap");iw.style.display=e==="inline"?"":"none";'
                . 'if(e==="inline"){var ifr=document.getElementById("efl-inline-iframe");ifr.src=iframeSrc();}'
                . '}'
                . 'document.querySelectorAll("input[name=embed_type]").forEach(function(r){r.onchange=updateEmbed;});'
                . 'document.querySelectorAll("input[name=popup_mode]").forEach(function(r){r.onchange=function(){var ifr=document.getElementById("efl-inline-iframe");if(ifr.src)ifr.src=iframeSrc();};});'
                . 'document.getElementById("efl-popup-btn").onclick=function(){_g_eflLon_flowMode=document.querySelector("input[name=popup_mode]:checked").value;_g_eflLon_showPopup();};'
                . '})();'
                . '</script>';
            $content .= '<p style="font-size:0.9em;color:#555;margin-top:2rem;">Przykłady przycisków: <a href="' . htmlspecialchars($popupExamplesUrl) . '" target="_blank">' . htmlspecialchars($popupExamplesUrl) . '</a></p>';
            renderPage('Demo EFL Leasing – Pop-up', $content, true);
            exit;
        }

        // Path: calculate – show results, option to continue to full
        if ($path === 'calculate') {
            $_SESSION['efl_from_calculate'] = [
                'token' => $token,
                'transactionId' => $transactionId,
                'offer' => $offer,
                'productsData' => $productsData,
                'nip' => $_POST['nip'] ?? '',
                'company_name' => $_POST['company_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'street' => $_POST['street'] ?? '',
                'house_number' => $_POST['house_number'] ?? '',
                'flat_number' => $_POST['flat_number'] ?? '',
                'postal_code' => $_POST['postal_code'] ?? '',
                'city' => $_POST['city'] ?? '',
                'country_code' => $_POST['country_code'] ?? 'PL',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'marketing_statement' => $_POST['marketing_statement'] ?? '',
                'custom_positive_url' => $_POST['custom_positive_url'] ?? '',
                'custom_negative_url' => $_POST['custom_negative_url'] ?? '',
            ];

            $content = '<h2>Wyniki kalkulacji</h2><p>ID transakcji: <code>' . htmlspecialchars($transactionId) . '</code></p>';
            $content .= '<form method="post"><input type="hidden" name="path" value="full"><input type="hidden" name="from_calculate" value="1">';
            $content .= '<fieldset><legend>Wybierz wariant oferty</legend>';
            $content .= variantSlidersHtml($offer->variants);
            $content .= '</fieldset><button type="submit">Kontynuuj do płatności (EFL)</button></form>';
            $content .= '<p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót do formularza</a></p>';

            if ($debugCapture !== null && $debugCapture->lastBody !== null) {
                $decoded = json_decode($debugCapture->lastBody, true);
                $content .= '<div class="section" style="margin-top:2rem;padding:1rem;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;">';
                $content .= '<h3>Debug: pełna odpowiedź API</h3>';
                $content .= '<p>Poniżej cała surowa odpowiedź ostatniego wywołania API (CalculateBasicOffer lub GetBaseData). Zawiera m.in. <code>status</code>, <code>basket</code>, <code>calculation</code> (wszystkie warianty z <code>total</code>), <code>partnerData</code>, <code>signProcessRedirectUrl</code>.</p>';
                if ($debugCapture->lastStatusCode !== null) {
                    $content .= '<p><strong>HTTP status:</strong> ' . htmlspecialchars((string) $debugCapture->lastStatusCode) . '</p>';
                }
                $debugJson = is_array($decoded) ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $debugCapture->lastBody;
                $content .= '<pre style="background:#f5f5f5;padding:1rem;font-size:11px;overflow-x:auto;max-height:40em;overflow-y:auto;">' . htmlspecialchars($debugJson) . '</pre>';
                $content .= '</div>';
            }

            renderPage('Demo EFL Leasing – Krok 2', $content, true);
            exit;
        }

        // Path: full – variant selection (step 2) or direct to verification
        $step = (int) ($_POST['step'] ?? 1);

        if ($fromCalculate) {
            $variantIndex = min(max(0, (int) ($_POST['variant_index'] ?? 0)), count($offer->variants) - 1);
            $variant = $offer->variants[$variantIndex];
            $calculationId = $offer->calculationId;
            $productsData = $calcData['productsData'] ?? $productsData;
            $_POST['nip'] = $calcData['nip'] ?? $_POST['nip'] ?? '';
            $_POST['company_name'] = $calcData['company_name'] ?? $_POST['company_name'] ?? '';
            $_POST['email'] = $calcData['email'] ?? $_POST['email'] ?? '';
            $_POST['phone'] = $calcData['phone'] ?? $_POST['phone'] ?? '';
            $_POST['street'] = $calcData['street'] ?? $_POST['street'] ?? '';
            $_POST['house_number'] = $calcData['house_number'] ?? $_POST['house_number'] ?? '';
            $_POST['flat_number'] = $calcData['flat_number'] ?? $_POST['flat_number'] ?? '';
            $_POST['postal_code'] = $calcData['postal_code'] ?? $_POST['postal_code'] ?? '';
            $_POST['city'] = $calcData['city'] ?? $_POST['city'] ?? '';
            $_POST['country_code'] = $calcData['country_code'] ?? $_POST['country_code'] ?? 'PL';
            $_POST['first_name'] = $calcData['first_name'] ?? $_POST['first_name'] ?? '';
            $_POST['last_name'] = $calcData['last_name'] ?? $_POST['last_name'] ?? '';
            if (($calcData['marketing_statement'] ?? '') === '1') {
                $_POST['marketing_statement'] = '1';
            }
        }

        if ($step === 1 && !$fromCalculate && count($offer->variants) > 1) {
            $_SESSION['efl_token'] = $token;
            $_SESSION['efl_transaction_id'] = $transactionId;
            $_SESSION['efl_calculation_id'] = $offer->calculationId;
            $_SESSION['efl_variants'] = $offer->variants;

            $variantForm = '<form method="post"><input type="hidden" name="step" value="2"><input type="hidden" name="path" value="full">';
            $variantForm .= hiddenFields([
                'products' => $productsData,
                'nip' => $_POST['nip'] ?? '',
                'company_name' => $_POST['company_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'street' => $_POST['street'] ?? '',
                'house_number' => $_POST['house_number'] ?? '',
                'flat_number' => $_POST['flat_number'] ?? '',
                'postal_code' => $_POST['postal_code'] ?? '',
                'city' => $_POST['city'] ?? '',
                'country_code' => $_POST['country_code'] ?? 'PL',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'marketing_statement' => $_POST['marketing_statement'] ?? '',
                'custom_positive_url' => $_POST['custom_positive_url'] ?? '',
                'custom_negative_url' => $_POST['custom_negative_url'] ?? '',
            ]);
            $variantForm .= '<h2>Krok 2: Wybierz wariant oferty</h2>';
            $variantForm .= '<fieldset><legend>Parametry oferty</legend>';
            $variantForm .= variantSlidersHtml($offer->variants);
            $variantForm .= '</fieldset><button type="submit">Kontynuuj do płatności</button></form>';
            $variantForm .= '<p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>';
            if ($debugCapture !== null && $debugCapture->lastBody !== null) {
                $decoded = json_decode($debugCapture->lastBody, true);
                $variantForm .= '<div class="section" style="margin-top:2rem;padding:1rem;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;">';
                $variantForm .= '<h3>Debug: pełna odpowiedź API</h3>';
                $variantForm .= '<p>Pełna surowa odpowiedź ostatniego wywołania API.</p>';
                if ($debugCapture->lastStatusCode !== null) {
                    $variantForm .= '<p><strong>HTTP status:</strong> ' . htmlspecialchars((string) $debugCapture->lastStatusCode) . '</p>';
                }
                $debugJson = is_array($decoded) ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $debugCapture->lastBody;
                $variantForm .= '<pre style="background:#f5f5f5;padding:1rem;font-size:11px;overflow-x:auto;max-height:40em;overflow-y:auto;">' . htmlspecialchars($debugJson) . '</pre>';
                $variantForm .= '</div>';
            }
            renderPage('Demo EFL Leasing – Krok 2', $variantForm, true);
            exit;
        }

        if ($step === 2 && isset($_SESSION['efl_token'], $_SESSION['efl_transaction_id'], $_SESSION['efl_calculation_id'], $_SESSION['efl_variants'])) {
            $token = $_SESSION['efl_token'];
            $transactionId = $_SESSION['efl_transaction_id'];
            $calculationId = $_SESSION['efl_calculation_id'];
            $variants = $_SESSION['efl_variants'];
            $variantIndex = min(max(0, (int) ($_POST['variant_index'] ?? 0)), count($variants) - 1);
            $variant = $variants[$variantIndex];
            unset($_SESSION['efl_token'], $_SESSION['efl_transaction_id'], $_SESSION['efl_calculation_id'], $_SESSION['efl_variants']);
        } else {
            $variant = $offer->variants[0];
            $calculationId = $offer->calculationId;
        }

        $calculationVariantId = $variant->calculationVariantId ?? null;
        if ($calculationId === null || $calculationVariantId === null) {
            renderPage('Demo EFL Leasing', '<p class="error">Nieprawidłowa odpowiedź kalkulacji.</p>');
            exit;
        }

        $client->acceptCalculation($transactionId, $calculationId, $calculationVariantId, null, $token);

        $nip = preg_replace('/\D/', '', (string) ($_POST['nip'] ?? ''));
        $companyName = trim((string) ($_POST['company_name'] ?? 'Demo Company'));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = preg_replace('/\D/', '', (string) ($_POST['phone'] ?? ''));
        $street = trim((string) ($_POST['street'] ?? ''));
        $houseNumber = trim((string) ($_POST['house_number'] ?? '1'));
        $flatNumber = trim((string) ($_POST['flat_number'] ?? '')) ?: null;
        $postalCode = trim((string) ($_POST['postal_code'] ?? '00-001'));
        $city = trim((string) ($_POST['city'] ?? ''));
        $countryCode = trim((string) ($_POST['country_code'] ?? 'PL')) ?: 'PL';
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $marketingStatement = isset($_POST['marketing_statement']);

        $verificationRequired = ['first_name' => $firstName, 'last_name' => $lastName, 'street' => $street, 'house_number' => $houseNumber, 'postal_code' => $postalCode, 'city' => $city, 'email' => $email];
        $missing = array_keys(array_filter($verificationRequired, fn ($v) => $v === ''));
        if ($missing !== []) {
            renderPage('Demo EFL Leasing', '<p class="error">Wymagane pola weryfikacji nie są wypełnione: ' . htmlspecialchars(implode(', ', $missing)) . '.</p><p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>');
            exit;
        }

        $guid = uniqid('', true);
        $companyBuilder = Company::builder($guid, $nip ?: '0000000000')
            ->addEmail(new EmailAddress($guid . '-email', $email ?: 'demo@example.com', 'work'))
            ->addPhone(new Phone($guid . '-phone', '+48', $phone ?: '123456789', 'mobile', 'mobile'))
            ->addAddress(new Address(
                $guid . '-addr',
                $companyName ?: 'Demo Company',
                'registered_office',
                $city ?: 'Warsaw',
                $street ?: 'Main St',
                $houseNumber ?: '1',
                $postalCode ?: '00-001',
                $countryCode,
                $flatNumber,
            ))
            ->addStatement(new CustomerDataStatement($guid . '-stmt', true, 'gdpr'));

        if ($marketingStatement) {
            $companyBuilder = $companyBuilder->addStatement(new CustomerDataStatement($guid . '-stmt-mkt', true, 'marketing'));
        }

        $company = $companyBuilder->build();
        $customerData = CustomerData::builder($transactionId, $calculationVariantId)
            ->withCompany($company)
            ->build();

        $client->submitCustomerData($customerData, $token);

        $verificationParams = VerificationInitializationParams::builder()
            ->withFirstName($firstName ?: 'Jan')
            ->withLastName($lastName ?: 'Kowalski')
            ->withResidenceAddressStreet($street ?: 'Main St')
            ->withResidenceAddressHouseNumber($houseNumber ?: '1')
            ->withResidenceAddressPostalCode($postalCode ?: '00-001')
            ->withResidenceAddressCity($city ?: 'Warsaw')
            ->withEmail($email ?: 'demo@example.com')
            ->build();

        $client->initializeIdentityVerification($transactionId, $verificationParams, $token);

        $redirectUrl = rtrim($eflFrontendBase, '/') . '/leaseOffer?transactionId=' . urlencode($transactionId) . '&token=' . urlencode($token);
        header('Location: ' . $redirectUrl);
        exit;
    } catch (ApiException|EflLeasingException $e) {
        $msg = '<p class="error">' . htmlspecialchars($e->getMessage()) . '</p>';
        if ($e instanceof ApiException && $e->getProblemDetails() !== null) {
            $pd = $e->getProblemDetails();
            if ($pd->detail !== null && $pd->detail !== '') {
                $msg .= '<pre style="background:#fff3cd;padding:1rem;">' . htmlspecialchars($pd->detail) . '</pre>';
            }
        }
        if ($debugCapture !== null && $debugCapture->lastBody !== null) {
            $msg .= '<pre style="background:#f5f5f5;padding:1rem;font-size:12px;">' . htmlspecialchars(mb_substr($debugCapture->lastBody, 0, 1500)) . '</pre>';
        }
        renderPage('Demo EFL Leasing', $msg . '<p><a href="' . htmlspecialchars($baseUrl) . '">← Powrót</a></p>');
    }
    exit;
}

// Step 1: Main form with path selector
$defaultProduct = [
    'product_name' => 'Samsung Galaxy S21FE DualSIM 5G 8/128GB',
    'product_id' => '1',
    'quantity' => '1',
    'price_net' => '2509.94',
    'vat_rate' => '23',
    'supplier_id' => '8951628481',
    'product_make' => 'Samsung',
    'product_model' => 'Galaxy S21FE DualSIM 5G',
    'product_class' => '1',
    'production_year' => '2024',
    'product_description' => 'Samsung Smartfon Galaxy S21FE DualSIM 5G 8/128GB Szary Enterprise',
    'asset_desc_1' => 'Smartfon',
];

$form = '<form method="post">';
$form .= '<fieldset><legend>Krok 1: Dane zamówienia</legend>';
$form .= '<h3>Produkty</h3>';
$form .= '<p style="font-size:0.9em;color:#555;margin-bottom:1rem;">Struktura produktu według <a href="https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage" target="_blank">dokumentacji sandboxa</a>: <code>supplierId</code>, <code>make</code>, <code>model</code>, <code>class</code>, <code>assetDesc1</code>, <code>description</code>. Przykład: supplierId <code>8951628481</code>, make <code>Samsung</code>, assetDesc1 <code>Smartfon</code>, class <code>1</code>.</p>';
$form .= '<div id="products-container">' . productRowHtml(0, $defaultProduct) . '</div>';
$form .= '<button type="button" id="btn-add-product" class="secondary">Dodaj produkt</button>';
$form .= '</fieldset>';

$form .= '<fieldset><legend>Firma</legend>';
$form .= '<div class="grid2"><label>NIP <input type="text" name="nip" value="1234567890" placeholder="10 cyfr"></label>';
$form .= '<label>Nazwa firmy <input type="text" name="company_name" value="Demo Sp. z o.o." required></label></div>';
$form .= '<div class="grid2"><label>E-mail <input type="email" name="email" value="demo@example.com" required></label>';
$form .= '<label>Telefon <input type="tel" name="phone" value="123456789" required></label></div>';
$form .= '<div class="grid2"><label>Ulica <input type="text" name="street" value="Marszałkowska" required></label>';
$form .= '<label>Nr domu <input type="text" name="house_number" value="1" required></label></div>';
$form .= '<div class="grid2"><label>Nr mieszkania <input type="text" name="flat_number" placeholder="Opcjonalnie"></label>';
$form .= '<label>Kod pocztowy <input type="text" name="postal_code" value="00-001" required></label></div>';
$form .= '<div class="grid2"><label>Miasto <input type="text" name="city" value="Warszawa" required></label>';
$form .= '<label>Kraj <input type="text" name="country_code" value="PL" required></label></div>';
$form .= '<label><input type="checkbox" name="marketing_statement" value="1"> Zgoda marketingowa</label>';
$form .= '</fieldset>';

$form .= '<fieldset><legend>Osoba (weryfikacja)</legend>';
$form .= '<div class="grid2"><label>Imię <input type="text" name="first_name" value="Jan" required></label>';
$form .= '<label>Nazwisko <input type="text" name="last_name" value="Kowalski" required></label></div>';
$form .= '</fieldset>';

$form .= '<fieldset><legend>Krok 2: Ścieżka płatności</legend>';
$form .= '<p>Wybierz sposób integracji z EFL (<a href="https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage" target="_blank">dokumentacja</a>):</p>';
$form .= '<div class="path-options">';
$form .= '<p style="margin:0.5rem 0 0.25rem;font-weight:bold;font-size:0.9em;">1. Przekierowanie – bez produktów (doc 2.3)</p>';
$form .= '<label class="path-option"><input type="radio" name="path" value="simple_productsSearch" ' . ($pathForForm === 'simple_productsSearch' ? 'checked' : '') . '> <strong>Wyszukiwarka produktów</strong> – init → przekierowanie na wyszukiwarkę EFL</label>';
$form .= '<p style="margin:0.5rem 0 0.25rem;font-weight:bold;font-size:0.9em;">2. Przekierowanie na kalkulację z koszykiem (doc 2.2)</p>';
$form .= '<label class="path-option"><input type="radio" name="path" value="basket" ' . ($pathForForm === 'basket' ? 'checked' : '') . '> <strong>Przekierowanie i kalkulacja</strong> – init + kalkulacja → przekierowanie na ofertę z koszykiem</label>';
$form .= '<p style="margin:0.5rem 0 0.25rem;font-weight:bold;font-size:0.9em;">3. Pop-up / widget (doc 2.1)</p>';
$form .= '<label class="path-option"><input type="radio" name="path" value="popup" ' . ($pathForForm === 'popup' ? 'checked' : '') . '> <strong>Pop-up lub osadzenie</strong> – init + kalkulacja → widget (popup lub bezpośrednio w stronie)</label>';
$form .= '<p style="margin:0.5rem 0 0.25rem;font-weight:bold;font-size:0.9em;">4. Rozszerzone flow</p>';
$form .= '<label class="path-option"><input type="radio" name="path" value="calculate" ' . ($pathForForm === 'calculate' ? 'checked' : '') . '> <strong>Tylko kalkulacja</strong> – podgląd oferty → opcjonalnie pełny checkout</label>';
$form .= '<label class="path-option"><input type="radio" name="path" value="full" ' . ($pathForForm === 'full' ? 'checked' : '') . '> <strong>Pełny checkout</strong> – wybór wariantu u nas → accept + submit + weryfikacja → przekierowanie</label>';
$form .= '</div>';
$form .= '<div id="simple-options-wrap" style="' . ($pathForForm === 'simple_productsSearch' ? '' : 'display:none;') . 'margin-top:0.5rem;padding:0.5rem;background:#f8f9fa;border-radius:4px;">';
$form .= '<p><label><input type="checkbox" name="debug_redirect" value="1"> Pokaż URL przed przekierowaniem (debug)</label></p>';
$form .= '<p style="font-size:0.9em;color:#555;margin-top:0.5rem;">Wyszukiwarka produktów nie wymaga produktów.</p>';
$form .= '</div>';
$form .= '</fieldset>';

$form .= '<fieldset><legend>Zaawansowane</legend>';
$form .= '<label>URL sukcesu <input type="url" name="custom_positive_url" value="' . htmlspecialchars($defaultPositiveUrl) . '" placeholder="URL powrotu"></label>';
$form .= '<label>URL anulowania <input type="url" name="custom_negative_url" value="' . htmlspecialchars($defaultNegativeUrl) . '" placeholder="URL powrotu"></label>';
$form .= '</fieldset>';

$form .= '<button type="submit">Kontynuuj</button>';
$form .= '</form>';

$form .= '<script>
(function(){
    var pathRadios=document.querySelectorAll("input[name=path]"),simpleWrap=document.getElementById("simple-options-wrap");
    function update(){var v=document.querySelector("input[name=path]:checked");if(v&&simpleWrap)simpleWrap.style.display=v.value==="simple_productsSearch"?"":"none";}
    if(pathRadios.length){pathRadios.forEach(function(r){r.onchange=update;});update();}
    var c=document.getElementById("products-container"),b=document.getElementById("btn-add-product");
    if(!c||!b)return;
    function u(){var x=c.querySelectorAll(".product-block");x.forEach(function(bl){var r=bl.querySelector(".btn-remove");if(r)r.disabled=x.length<=1;});}
    function r(){var x=c.querySelectorAll(".product-block");x.forEach(function(bl,i){var h=bl.querySelector("h3");if(h)h.textContent="Produkt "+(i+1);bl.querySelectorAll("input").forEach(function(inp){var n=inp.name;if(n&&n.indexOf("products[")===0){var m=n.match(/^products\[\d+\](.*)$/);if(m)inp.name="products["+i+"]"+m[1];}});});u();}
    b.onclick=function(){var f=c.querySelector(".product-block");if(!f)return;var cl=f.cloneNode(true);cl.querySelectorAll("input").forEach(function(i){if((i.name||"").indexOf("quantity")!==-1)i.value="1";else if(i.type!=="checkbox")i.value="";});c.appendChild(cl);r();};
    c.onclick=function(e){if(e.target.classList.contains("btn-remove")&&!e.target.disabled){if(c.querySelectorAll(".product-block").length>1){e.target.closest(".product-block").remove();r();}}};
    u();
})();
</script>';

$intro = '<div style="margin-bottom:1.5rem;padding:1rem;background:#f0f7ff;border:1px solid #c5d9f0;border-radius:4px;">';
$intro .= '<p style="margin:0 0 0.5rem;font-weight:bold;">Prezentacja działania SDK EFL Leasing</p>';
$intro .= '<p style="margin:0;font-size:0.95em;line-height:1.5;">Ta strona jest przykładem kompleksowej integracji z EFL Leasing – cała strona, wszystkie kroki procesu i połączenie z EFL to <strong>jeden plik PHP</strong>. To możliwe dzięki SDK, które upraszcza integrację i dostarcza gotowe narzędzia do dodania płatności leasingowych w projektach opartych na PHP. SDK pozwala szybko zintegrować sklep lub stronę z ofertą EFL bez konieczności samodzielnego budowania wszystkich elementów integracji. <a href="' . htmlspecialchars($docsBaseUrl) . '" target="_blank" rel="noopener">Dokumentacja SDK →</a></p>';
$intro .= '</div>';
$intro .= '<p>Wypełnij dane zamówienia (produkty, firma, osoba). Wybierz ścieżkę płatności na dole. EFL Leasing działa jako metoda płatności – zbierasz zamówienie, a następnie przekierowujesz klienta do EFL w celu wyboru oferty i finalizacji.</p>';
renderPage('Demo EFL Leasing SDK', $intro . $form, true);
