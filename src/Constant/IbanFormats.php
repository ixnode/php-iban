<?php

/*
 * This file is part of the ixnode/php-iban project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ixnode\PhpIban\Constant;

use Ixnode\PhpTimezone\Constants\CountryEurope;

/**
 * Class IbanFormats
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final class IbanFormats
{
    /* @see https://en.wikipedia.org/wiki/International_Bank_Account_Number#IBAN_formats_by_country */
    final public const IBAN_FORMATS = [
        'AD' => 'ADkk bbbb ssss cccc cccc cccc',
        'AE' => 'AEkk bbbc cccc cccc cccc ccc',
        'AL' => 'ALkk bbbs sssx cccc cccc cccc cccc',
        CountryEurope::COUNTRY_CODE_AT => 'ATkk bbbb bccc cccc cccc',
        'AZ' => 'AZkk bbbb cccc cccc cccc cccc cccc',
        'BA' => 'BAkk bbbs sscc cccc ccxx',
        'BE' => 'BEkk bbbc cccc ccxx',
        'BG' => 'BGkk bbbb ssss ttcc cccc cc',
        'BH' => 'BHkk bbbb cccc cccc cccc cc',
        'BR' => 'BRkk bbbb bbbb ssss sccc cccc ccct n',
        'BY' => 'BYkk bbbb aaaa cccc cccc cccc cccc',
        CountryEurope::COUNTRY_CODE_CH => 'CHkk bbbb bccc cccc cccc c',
        'CR' => 'CRkk 0bbb cccc cccc cccc cc',
        'CY' => 'CYkk bbbs ssss cccc cccc cccc cccc',
        'CZ' => 'CZkk bbbb pppp ppcc cccc cccc',
        CountryEurope::COUNTRY_CODE_DE => 'DEkk bbbb bbbb cccc cccc cc',
        'DK' => 'DKkk bbbb cccc cccc cx',
        'DO' => 'DOkk bbbb cccc cccc cccc cccc cccc',
        'EE' => 'EEkk bbss cccc cccc cccx',
        'EG' => 'EGkk bbbb ssss cccc cccc cccc cccc c',
        'ES' => 'ESkk bbbb ssss xxcc cccc cccc',
        'FI' => 'FIkk bbbb bbcc cccc cx',
        'FO' => 'FOkk bbbb cccc cccc cx',
        CountryEurope::COUNTRY_CODE_FR => 'FRkk bbbb bsss sscc cccc cccc cxx',
        'GB' => 'GBkk bbbb ssss sscc cccc cc',
        'GE' => 'GEkk bbcc cccc cccc cccc cc',
        'GI' => 'GIkk bbbb cccc cccc cccc ccc',
        'GL' => 'GLkk bbbb cccc cccc cx',
        'GR' => 'GRkk bbbs sssc cccc cccc cccc ccc',
        'GT' => 'GTkk bbbb mmtt cccc cccc cccc cccc',
        'HR' => 'HRkk bbbb bbbc cccc cccc c',
        'HU' => 'HUkk bbbs sssx cccc cccc cccc cccx',
        'IE' => 'IEkk qqqq bbbb bbcc cccc cc',
        'IL' => 'ILkk bbbs sscc cccc cccc ccc',
        'IQ' => 'IQkk bbbb sssc cccc cccc ccc',
        'IS' => 'ISkk bbss ttcc cccc iiii iiii ii',
        'IT' => 'ITkk xbbb bbss sssc cccc cccc ccc',
        'JO' => 'JOkk bbbb ssss cccc cccc cccc cccc cc',
        'KW' => 'KWkk bbbb cccc cccc cccc cccc cccc cc',
        'KZ' => 'KZkk bbbc cccc cccc cccc',
        'LB' => 'LBkk bbbb cccc cccc cccc cccc cccc',
        'LC' => 'LCkk bbbb cccc cccc cccc cccc cccc cccc',
        CountryEurope::COUNTRY_CODE_LI => 'LIkk bbbb bccc cccc cccc c',
        'LT' => 'LTkk bbbb bccc cccc cccc',
        'LU' => 'LUkk bbbc cccc cccc cccc',
        'LV' => 'LVkk bbbb cccc cccc cccc c',
        'LY' => 'LYkk bbbs sscc cccc cccc cccc c',
        'MC' => 'MCkk bbbb bsss sscc cccc cccc cxx',
        'MD' => 'MDkk bbcc cccc cccc cccc cccc',
        'ME' => 'MEkk bbbc cccc cccc cccc xx',
        'MK' => 'MKkk bbbc cccc cccc cxx',
        'MR' => 'MRkk bbbb bsss sscc cccc cccc cxx',
        'MT' => 'MTkk bbbb ssss sccc cccc cccc cccc ccc',
        'MU' => 'MUkk bbbb bbss cccc cccc cccc 000m mm',
        'NL' => 'NLkk bbbb cccc cccc cc',
        'NO' => 'NOkk bbbb cccc ccx',
        'PK' => 'PKkk bbbb cccc cccc cccc cccc',
        'PL' => 'PLkk bbbs sssx cccc cccc cccc cccc',
        'PS' => 'PSkk bbbb cccc cccc cccc cccc cccc c',
        'PT' => 'PTkk bbbb ssss cccc cccc cccx x',
        'QA' => 'QAkk bbbb cccc cccc cccc cccc cccc c',
        'RO' => 'ROkk bbbb cccc cccc cccc cccc',
        'RS' => 'RSkk bbbc cccc cccc cccc xx',
        'RU' => 'RUkk bbbb bbbb bsss sscc cccc cccc cccc c',
        'SA' => 'SAkk bbcc cccc cccc cccc cccc',
        'SC' => 'SCkk bbbb bb ss cccc cccc cccc cccc mmm',
        'SD' => 'SDkk bbcc cccc cccc cc',
        'SE' => 'SEkk bbbc cccc cccc cccc cccx',
        'SI' => 'SIkk bbss sccc cccc cxx',
        'SK' => 'SKkk bbbb pppp ppcc cccc cccc',
        'SM' => 'SMkk xbbb bbss sssc cccc cccc ccc',
        'ST' => 'STkk bbbb ssss cccc cccc cccc c',
        'SV' => 'SVkk bbbb cccc cccc cccc cccc cccc',
        'TL' => 'TLkk bbbc cccc cccc cccc cxx',
        'TN' => 'TNkk bbss sccc cccc cccc ccxx',
        'TR' => 'TRkk bbbb b0cc cccc cccc cccc cc',
        'UA' => 'UAkk bbbb bbcc cccc cccc cccc cccc c',
        'VA' => 'VAkk bbbc cccc cccc cccc cc',
        'VG' => 'VGkk bbbb cccc cccc cccc cccc',
        'XK' => 'XKkk bbbb cccc cccc cccc',
    ];
}
