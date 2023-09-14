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

namespace Ixnode\PhpIban;

use Ixnode\PhpIban\Constant\IbanFormats;
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;

/**
 * Class IbanFormat
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-02)
 * @since 0.1.0 (2023-09-02) First version.
 */
final class IbanFormat
{
    /* AT, CH, DE, etc. */
    final public const KEY_COUNTRY_CODE = 'country-code';


    /* Balance account number: a */
    final public const KEY_BALANCE_ACCOUNT_NUMBER = 'balance-account-number';
    final public const CODE_BALANCE_ACCOUNT_NUMBER = 'a';


    /* National bank code: b */
    final public const KEY_NATIONAL_BANK_CODE = 'national-bank-code';
    final public const CODE_NATIONAL_BANK_CODE = 'b';


    /* Account number: c */
    final public const KEY_ACCOUNT_NUMBER = 'account-number';
    final public const CODE_ACCOUNT_NUMBER = 'c';


    /* National identification number: i */
    final public const KEY_NATIONAL_IDENTIFICATION_NUMBER = 'national-identification-number';
    final public const CODE_NATIONAL_IDENTIFICATION_NUMBER = 'i';


    /* IBAN check digits: k */
    final public const KEY_IBAN_CHECK_DIGITS = 'iban-check-digits';
    final public const CODE_IBAN_CHECK_DIGITS = 'k';


    /* Currency code: m */
    final public const KEY_CURRENCY_CODE = 'currency-code';
    final public const CODE_CURRENCY_CODE = 'm';


    /* Owner account number: n */
    final public const KEY_OWNER_ACCOUNT_NUMBER = 'owner-account-number';
    final public const CODE_OWNER_ACCOUNT_NUMBER = 'n';


    /* Account number prefix: p */
    final public const KEY_ACCOUNT_NUMBER_PREFIX = 'account-number-prefix';
    final public const CODE_ACCOUNT_NUMBER_PREFIX = 'p';


    /* BIC bank code: q */
    final public const KEY_BIC_BANK_CODE = 'bic-bank-code';
    final public const CODE_BIC_BANK_CODE = 'q';


    /* Branch code: s */
    final public const KEY_BRANCH_CODE = 'branch-code';
    final public const CODE_BRANCH_CODE = 's';


    /* Account type: t */
    final public const KEY_ACCOUNT_TYPE = 'account-type';
    final public const CODE_ACCOUNT_TYPE = 't';


    /* National check digits: x */
    final public const KEY_NATIONAL_CHECK_DIGITS = 'national-check-digits';
    final public const CODE_NATIONAL_CHECK_DIGITS = 'x';


    /* Always zero: 0 */
    final public const ALWAYS_ZERO = '0';


    /* IBAN formatted */
    final public const KEY_IBAN_FORMATTED = 'iban-formatted';


    protected const EMPTY_CHECK_DIGITS = '00';

    protected string|null $lastError = null;


    /**
     * @param string $countryCode
     */
    public function __construct(private readonly string $countryCode)
    {
    }

    /**
     * Returns the IBAN format without spaces by given country.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getIbanFormat(): string
    {
        if (!array_key_exists($this->countryCode, IbanFormats::IBAN_FORMATS)) {
            throw new IbanParseException(sprintf('The given country "%s" is not supported yet.', $this->countryCode));
        }

        return str_replace(' ', '', IbanFormats::IBAN_FORMATS[$this->countryCode]);
    }

    /**
     * Returns the IBAN format codes.
     *
     * @param bool $withoutIbanCheckDigits
     * @param bool $withZero
     * @return array<int, string>
     * @throws IbanParseException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function getIbanFormatCodes(bool $withoutIbanCheckDigits = false, bool $withZero = false): array
    {
        $ibanFormat = $this->getIbanFormat();

        $ibanFormat = substr($ibanFormat, $withoutIbanCheckDigits ? 4 : 2);

        $characters = str_split($ibanFormat);

        $ibanFormatCodesWithZero = array_values(array_unique($characters));

        return array_values(array_diff($ibanFormatCodesWithZero, $withZero ? [] : [self::ALWAYS_ZERO]));
    }

    /**
     * Returns the IBAN format key given by IBAN format code.
     *
     * @param string $code
     * @return string
     * @throws IbanParseException
     */
    private function getIbanFormatKeyByCode(string $code): string
    {
        return match ($code) {
            self::CODE_BALANCE_ACCOUNT_NUMBER => self::KEY_BALANCE_ACCOUNT_NUMBER,
            self::CODE_NATIONAL_BANK_CODE => self::KEY_NATIONAL_BANK_CODE,
            self::CODE_ACCOUNT_NUMBER => self::KEY_ACCOUNT_NUMBER,
            self::CODE_NATIONAL_IDENTIFICATION_NUMBER => self::KEY_NATIONAL_IDENTIFICATION_NUMBER,
            self::CODE_IBAN_CHECK_DIGITS => self::KEY_IBAN_CHECK_DIGITS,
            self::CODE_CURRENCY_CODE => self::KEY_CURRENCY_CODE,
            self::CODE_OWNER_ACCOUNT_NUMBER => self::KEY_OWNER_ACCOUNT_NUMBER,
            self::CODE_ACCOUNT_NUMBER_PREFIX => self::KEY_ACCOUNT_NUMBER_PREFIX,
            self::CODE_BIC_BANK_CODE => self::KEY_BIC_BANK_CODE,
            self::CODE_BRANCH_CODE => self::KEY_BRANCH_CODE,
            self::CODE_ACCOUNT_TYPE => self::KEY_ACCOUNT_TYPE,
            self::CODE_NATIONAL_CHECK_DIGITS => self::KEY_NATIONAL_CHECK_DIGITS,
            default => throw new IbanParseException(sprintf('The given IBAN format code "%s" is not supported yet.', $code)),
        };
    }

    /**
     * Returns the IBAN format keys by given IBAN format string.
     *
     * @param array<int, string> $except
     * @return array<int, string>
     * @throws IbanParseException
     */
    public function getIbanFormatPropertyKeys(array $except = []): array
    {
        $propertyKeys = [];

        foreach ($this->getIbanFormatCodes(true) as $code) {
            $propertyKeys[] = $this->getIbanFormatKeyByCode($code);
        }

        return array_values(array_diff($propertyKeys, $except));
    }

    /**
     * Returns all IBAN format keys.
     *
     * @param array<int, string> $except
     * @return array<int, string>
     */
    public function getIbanFormatPropertyKeysAll(array $except = []): array
    {
        $propertyKeys = [
            IbanFormat::KEY_NATIONAL_BANK_CODE,
            IbanFormat::KEY_ACCOUNT_NUMBER,
            IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER,
            IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER,
            IbanFormat::KEY_CURRENCY_CODE,
            IbanFormat::KEY_OWNER_ACCOUNT_NUMBER,
            IbanFormat::KEY_ACCOUNT_NUMBER_PREFIX,
            IbanFormat::KEY_BIC_BANK_CODE,
            IbanFormat::KEY_BRANCH_CODE,
            IbanFormat::KEY_ACCOUNT_TYPE,
            IbanFormat::KEY_NATIONAL_CHECK_DIGITS,
        ];

        return array_values(array_diff($propertyKeys, $except));
    }

    /**
     * Returns the country abbreviation translate string.
     *
     * - DE: 1314
     * - etc.
     *
     * @param string $value
     * @return string
     */
    private function calculateCodeNumber(string $value): string
    {
        $countryCodeNumber = '';

        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $valueExplode = str_split(strtolower($value));

        foreach ($valueExplode as $check) {
            $position = strpos($alphabet, $check);

            if ($position === false) {
                $countryCodeNumber .= $check;
                continue;
            }

            $countryCodeNumber .= strpos($alphabet, $check) + 10;
        }

        return $countryCodeNumber;
    }

    /**
     * Builds an IBAN from given Account object.
     *
     * @param Account $account
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIban(Account $account): string|null
    {
        $ibanFormatCodes = $this->getIbanFormatCodes();

        $iban = $this->getIbanFormat();

        return $this->translateIbanFormat($account, $ibanFormatCodes, $iban);
    }

    /**
     * Builds an IBAN from given Account object.
     *
     * @param Account $account
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIbanRaw(Account $account): string|null
    {
        $ibanFormatCodes = $this->getIbanFormatCodes(true);

        $iban = $this->getIbanFormat();

        $iban = substr($iban, 4).$this->countryCode.self::EMPTY_CHECK_DIGITS;

        $translateIbanFormat = $this->translateIbanFormat($account, $ibanFormatCodes, $iban);

        if (is_null($translateIbanFormat)) {
            return null;
        }

        return $this->calculateCodeNumber($translateIbanFormat);
    }

    /**
     * Translates the given iban format.
     *
     * @param Account $account
     * @param array<int, string> $ibanFormatCodes
     * @param string $ibanFormat
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    private function translateIbanFormat(Account $account, array $ibanFormatCodes, string $ibanFormat): string|null
    {
        $ibanFormatParsed = $ibanFormat;

        foreach ($ibanFormatCodes as $ibanFormatCode) {
            $count = substr_count($ibanFormatParsed, $ibanFormatCode);

            if ($count <= 0) {
                continue;
            }

            $value = match ($ibanFormatCode) {
                IbanFormat::CODE_IBAN_CHECK_DIGITS => $account->getIbanCheckDigits(),
                IbanFormat::CODE_NATIONAL_BANK_CODE => $account->getNationalBankCode(),
                IbanFormat::CODE_ACCOUNT_NUMBER => $account->getAccountNumber(),
                IbanFormat::CODE_BALANCE_ACCOUNT_NUMBER => $account->getBalanceAccountNumber(),
                IbanFormat::CODE_NATIONAL_IDENTIFICATION_NUMBER => $account->getNationalIdentificationNumber(),
                IbanFormat::CODE_CURRENCY_CODE => $account->getCurrencyCode(),
                IbanFormat::CODE_OWNER_ACCOUNT_NUMBER => $account->getOwnerAccountNumber(),
                IbanFormat::CODE_ACCOUNT_NUMBER_PREFIX => $account->getAccountNumberPrefix(),
                IbanFormat::CODE_BIC_BANK_CODE => $account->getBicBankCode(),
                IbanFormat::CODE_BRANCH_CODE => $account->getBranchCode(),
                IbanFormat::CODE_ACCOUNT_TYPE => $account->getAccountType(),
                IbanFormat::CODE_NATIONAL_CHECK_DIGITS => $account->getNationalCheckDigits(),
                default => throw new IbanParseException(sprintf('Unsupported iban format code "%s".', $ibanFormatCode)),
            };

            if (is_null($value)) {
                match (true) {
                    $account->hasLastError() => $this->setLastError($account->getLastError()),
                    default => $this->setLastError(sprintf('Unsupported property within the Account object given: "%s".', $ibanFormatCode)),
                };
                return null;
            }

            if (strlen($value) > $count) {
                $this->setLastError(sprintf('The given value "%s" is too long (%s: %s).', $value, $ibanFormatCode, $ibanFormat));
                return null;
            }

            $value = str_pad($value, $count, '0', STR_PAD_LEFT);

            $ibanFormatParsed = str_replace(str_repeat($ibanFormatCode, $count), $value, $ibanFormatParsed);
        }

        return $ibanFormatParsed;
    }

    /**
     * Sets the last error message.
     *
     * @param string $lastError
     * @return void
     */
    private function setLastError(string $lastError): void
    {
        $this->lastError = $lastError;
    }

    /**
     * Returns the last error.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getLastError(): string
    {
        if (is_null($this->lastError)) {
            throw new IbanParseException('There is no last error set.');
        }

        return $this->lastError;
    }

    /**
     * Returns whether the last error is set.
     *
     * @return bool
     */
    public function hasLastError(): bool
    {
        return !is_null($this->lastError);
    }

    /**
     * Alias of hasLastError().
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->hasLastError();
    }
}
