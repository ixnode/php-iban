# PHP IBAN

[![Release](https://img.shields.io/github/v/release/ixnode/php-iban)](https://github.com/ixnode/php-iban/releases)
[![](https://img.shields.io/github/release-date/ixnode/php-iban)](https://github.com/ixnode/php-iban/releases)
![](https://img.shields.io/github/repo-size/ixnode/php-iban.svg)
[![PHP](https://img.shields.io/badge/PHP-^8.2-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=flat)](https://www.php.net/supported-versions.php)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%20Max-777bb3.svg?style=flat)](https://phpstan.org/user-guide/rule-levels)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-Unit%20Tests-6b9bd2.svg?style=flat)](https://phpunit.de)
[![PHPCS](https://img.shields.io/badge/PHPCS-PSR12-416d4e.svg?style=flat)](https://www.php-fig.org/psr/psr-12/)
[![PHPMD](https://img.shields.io/badge/PHPMD-ALL-364a83.svg?style=flat)](https://github.com/phpmd/phpmd)
[![Rector - Instant Upgrades and Automated Refactoring](https://img.shields.io/badge/Rector-PHP%208.2-73a165.svg?style=flat)](https://github.com/rectorphp/rector)
[![LICENSE](https://img.shields.io/github/license/ixnode/php-api-version-bundle)](https://github.com/ixnode/php-api-version-bundle/blob/master/LICENSE)

> This library provides a converter for account number, bank code and IBAN.

## 1. Usage

```php
use Ixnode\PhpIban\Account;
use Ixnode\PhpIban\Iban;
use Ixnode\PhpIban\Validator;
```

### 1.1 IBAN parser example

```php
$iban = 'DE02120300000000202051';
$validator = new Validator(new Iban($iban));

print $validator->isValid() ? 'YES' : 'NO';
// (string) YES

print $validator->getAccountNumber();
// (string) 0000202051

etc.
```

### 1.2 Account number and bank code converter example

```php
$accountNumber = '0000202051';
$bankCode = '12030000';
$countryCode = 'DE';
$validator = new Validator(new AccountNumber($accountNumber, $bankCode, $countryCode));

print $validator->getIban();
// (string) DE02120300000000202051

print $validator->getIbanFormatted();
// (string) DE02 1203 0000 0000 2020 51
```

## 2. Supported countries

* AL (Albania)
* AT (Austria)
* CH (Switzerland)
* DE (Germany)
* ES (Spain)
* FR (France)
* LI (Liechtenstein)
* PT (Portugal)

See https://en.wikipedia.org/wiki/International_Bank_Account_Number#IBAN_formats_by_country to add more countries.

## 3. Installation

```bash
composer require ixnode/php-iban
```

```bash
vendor/bin/php-iban -V
```

```bash
0.1.0 (2023-09-01 21:09:52) - Björn Hempel <bjoern@hempel.li>
```

## 4. Command line tool

### 4.1 Check IBAN number

> Used to quickly check a given IBAN number.

```bash
bin/console iban:validate DE02120300000000202051
```

or within your composer project:

```bash
bin/console iban:validate FR7630027175330002005370159
```

```bash

Given IBAN:     FR7630027175330002005370159

Parsed IBAN
-----------
IBAN:                   FR7630027175330002005370159
IBAN (formatted):       FR76 3002 7175 3300 0200 5370 159
Valid:                  YES
Last error:             N/A
Country:                FR (France)
Checksum:               76
National bank code:     30027
Branch code:            17533
Account number:         00020053701
National check digits:  59

```

### 4.2 Generate IBAN number

> Used to quickly generate an IBAN number.

```bash
bin/console account-number:validate 0000202051 12030000
```

or within your composer project:

```bash
vendor/bin/php-iban account-number:validate 0000202051 12030000
```

```bash

Given account number: 0000202051
Given bank code:      12030000
Given country code:   DE

Parsed IBAN
-----------
IBAN:           DE02120300000000202051
Valid:          YES
Last error:     N/A
Country:        DE
Checksum:       02
Account number: 0000202051
Bank number:    12030000

```

## 5. Library development

```bash
git clone git@github.com:ixnode/php-iban.git && cd php-iban
```

```bash
composer install
```

```bash
composer test
```

## 6. License

This library is licensed under the MIT License - see the [LICENSE](/LICENSE) file for details.
