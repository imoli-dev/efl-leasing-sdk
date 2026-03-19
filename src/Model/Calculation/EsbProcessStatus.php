<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

/**
 * Mirrors the EsbProcessStatus enum from the EFL API.
 */
enum EsbProcessStatus: string
{
    case TransactionInitialized = 'TransactionInitialized';
    case WaitingForResponse = 'WaitingForResponse';
    case Kalkulacja = 'Kalkulacja';
    case FormularzNip = 'FormularzNIP';
    case DaneKontrahenta = 'Dane_kontrahenta';
    case Formularz = 'Formularz';
    case Lead = 'Lead';
    case SlownikiWyszukiwania = 'Słowniki_wyszukiwania';
    case MarkaModel = 'MarkaModel';
    case DoWeryfikacjiBm = 'Do_weryfikacji_BM';
    case DoWeryfikacjiRdo = 'Do_weryfikacji_RDO';
    case BmWeryfikacjaKlientaPozytywna = 'BM_weryfikacja_klienta_pozytywna';
    case End = 'END';
    case ErrorEnd = 'error_end';
    case DecyzjaPozytywna = 'decyzja_pozytywna';
    case TemporaryEnd = 'temporary_end';
    case SposobPodpisuUmowy = 'Sposob_podpisu_umowy';
    case DaneKontrahentaDoWydaniaPodpisu = 'Dane_kontrahenta_do_wyd_podpisu';
    case DecyzjaPozytywnaPowrot = 'Decyzja_pozytywna_powrot';
    case LeadBaw = 'leadBAW';
    case PrzekazanieDokumentow = 'Przekazanie_dokumentow';
    case PodpisanieUmowyEflBezWp = 'Podpisanie_umowy_EFL_bezWP';
    case PodpisanieUmowyEflWp = 'Podpisanie_umowy_EFL_WP';
    case Error = 'Error';
    case BmWplataWlasna = 'BM_wplata_wlasna';
    case KodPowrotu = 'kod_powrotu';
}
