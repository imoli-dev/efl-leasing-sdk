# EFL Leasing SDK Demo

PoC demonstrating the EFL Leasing SDK flow: a form that collects order and customer data, submits it via the SDK, and redirects the user to the EFL sandbox for identity verification.

**Payment paths (select in form):**

Per the [EFL documentation](https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage):

1. **Redirect without products (doc 2.3)** – Wyszukiwarka produktów (product search)

2. **Redirect to calculation with basket (doc 2.2)** – Przekierowanie i kalkulacja – init + calculation → redirect to offer with basket

3. **Pop-up / widget (doc 2.1)** – init + calculation → widget (popup overlay or inline embedding)

4. **Extended flows** – Calculate only (preview) or Full checkout (variant selection, accept, submit, verification → redirect)

Features:
- Full product details (make, model, class, production year, asset descriptions)
- Variant selection when multiple offer variants are returned
- Company data with flat number and optional marketing consent
- Custom return URLs and basket calculation options

## Requirements

- PHP 8.1+
- Composer

## Setup

1. Install dependencies:

   ```bash
   composer install
   ```

2. Copy `.env.example` to `.env` and fill in your EFL sandbox credentials:

   ```bash
   cp .env.example .env
   ```

   Edit `.env` and set:

   - `EFL_API_KEY` – your sandbox API key
   - `EFL_PARTNER_ID` – your partner identifier
   - `EFL_BASE_URL` – (optional) base URL for return URLs. If empty, detected from the request. For sandbox, a public URL (e.g. ngrok) may be required when localhost is rejected.

   Obtain credentials from the [EFL sandbox documentation](https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage).

**Product data:** The [sandbox documentation](https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage) (section 4) shows example product structures for `CalculateBasicOffer`: `supplierId`, `make`, `model`, `class`, `assetDesc1`, `description`. Example: supplierId `8951628481`, make `Samsung`, assetDesc1 `Smartfon`, class `1`.

**Debugging:** Set `EFL_DEBUG=1` in `.env` or add `?debug=1` to the URL to see raw API responses on the calculation results page and when errors occur (e.g. when no offer variants are returned). Use this to verify which fields contain the residual value (kwota wykupu).

**Troubleshooting "Ups… coś poszło nie tak" on EFL sandbox:**
- Ensure `EFL_API_KEY` and `EFL_PARTNER_ID` are correct (from sandbox documentation).
- The redirect to sandbox requires both `transactionId` and `token` in the URL; the demo uses the correct format.
- **Return URLs (success/cancel)** – if using `localhost`, the sandbox may reject it. Use `EFL_BASE_URL` with a public URL (see ngrok section below) or ask EFL to whitelist your URLs.
- **Simple redirect path** – use "Simple redirect" with "Pokaż URL przed przekierowaniem" to verify the redirect URL. Try `productsSearch` (wyszukiwarka) instead of `leaseOffer` when no products were sent – `leaseOffer` may expect prior calculation.
- For full checkout: verify customer data (valid NIP, complete address) and that the selected offer variant exists.

## Running the demo

Start the built-in PHP server:

```bash
php -S localhost:8000
```

Open [http://localhost:8000](http://localhost:8000) in your browser. Fill in the form and submit. You will be redirected to the EFL sandbox to complete the leasing flow.

### Deployment (e.g. MyDevil)

The demo loads the SDK from GitHub. For deployment:

1. Upload the demo folder (including `composer.json`).
2. If the SDK repo is **private**, configure a GitHub token (read-only is enough):
   ```bash
   export COMPOSER_AUTH='{"github-oauth":{"github.com":"ghp_YOUR_TOKEN"}}'
   ```
   Or: `composer config github-oauth.github.com ghp_YOUR_TOKEN`
3. Run: `composer install --no-dev` (or `composer update --no-dev` if switching from path repo).
4. Add `.env` with `EFL_API_KEY` and `EFL_PARTNER_ID`.

**Local development** with the full SDK repo: add a path repository before the vcs one in `composer.json` to use local SDK changes.

### Testing with ngrok (when localhost is rejected)

If the sandbox rejects localhost return URLs:

1. Start the PHP server: `php -S localhost:8000`
2. Run `ngrok http 8000` in a separate terminal.
3. Copy the HTTPS URL (e.g. `https://abc123.ngrok.io`).
4. Either:
   - Open the demo via the ngrok URL (return URLs will use it automatically), or
   - Add to `.env`: `EFL_BASE_URL=https://abc123.ngrok.io` and open via localhost (return URLs will be built from the env var).
