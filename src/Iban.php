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
 * Class Iban
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final class Iban
{
    /* Balance account number */
    /** @phpstan-ignore-next-line */
    private const BALANCE_ACCOUNT_NUMBER = 'a';

    /* National bank code */
    private const NATIONAL_BANK_CODE = 'b';

    /* Account number */
    private const ACCOUNT_NUMBER = 'c';

    /* National identification number */
    /** @phpstan-ignore-next-line */
    private const NATIONAL_IDENTIFICATION_NUMBER = 'i';

    /* IBAN check digits */
    private const IBAN_CHECK_DIGITS = 'k';

    /* Currency code */
    /** @phpstan-ignore-next-line */
    private const CURRENCY_CODE = 'm';

    /* Owner account number */
    /** @phpstan-ignore-next-line */
    private const OWNER_ACCOUNT_NUMBER = 'n';

    /* BIC bank code */
    /** @phpstan-ignore-next-line */
    private const BIC_BANK_CODE = 'q';

    /* Branch code */
    private const BRANCH_CODE = 's';

    /* Account type */
    /** @phpstan-ignore-next-line */
    private const ACCOUNT_TYPE = 't';

    /* National check digits */
    private const NATIONAL_CHECK_DIGITS = 'x';

    /* Always zero */
    /** @phpstan-ignore-next-line */
    private const ALWAYS_ZERO = '0';

    private const SUPPORTED_CODES = [
        self::NATIONAL_BANK_CODE,
        self::BRANCH_CODE,
        self::ACCOUNT_NUMBER,
        self::IBAN_CHECK_DIGITS,
        self::NATIONAL_CHECK_DIGITS,
    ];

    private string|null $lastError = null;

    private bool $valid = false;

    private string|null $countryCode = null;

    private string|null $ibanCheckDigits = null;

    private string|null $nationalBankCode = null;

    private string|null $branchCode = null;

    private string|null $accountNumber = null;

    private string|null $nationalCheckDigits = null;

    /**
     * @param string $iban
     * @throws IbanParseException
     * @throws AccountParseException
     */
    public function __construct(private readonly string $iban)
    {
        if (!$this->parseIban()) {
            $this->countryCode = null;
            $this->ibanCheckDigits = null;
            $this->nationalBankCode = null;
            $this->accountNumber = null;
            $this->branchCode = null;
        }
    }

    /**
     * Parses the given IBAN number.
     *
     * @return bool
     * @throws AccountParseException
     * @throws IbanParseException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function parseIban(): bool
    {
        $iban = $this->iban;
        $countryCode = strtoupper(substr($iban, 0, 2));

        if (!array_key_exists($countryCode, IbanFormats::IBAN_FORMATS)) {
            $this->lastError = sprintf('The given country "%s" is not supported yet.', $countryCode);
            $this->valid = false;

            return false;
        }

        $ibanFormat = $this->getIbanFormat($countryCode);
        $checkFormat = $this->checkIbanFormat($ibanFormat);

        if ($checkFormat !== '') {
            $this->lastError = sprintf('The given country "%s" is not supported yet (Unsupported iban format chars: "%s").', $countryCode, $checkFormat);
            $this->valid = false;

            return false;
        }

        if (strlen($iban) !== strlen($ibanFormat)) {
            $this->lastError = sprintf('Invalid length of IBAN given: "%s" (expected: "%s").', $iban, $ibanFormat);
            $this->valid = false;

            return false;
        }

        $this->countryCode = $countryCode;
        $this->ibanCheckDigits = $this->extractInformation($countryCode, self::IBAN_CHECK_DIGITS);
        $this->nationalBankCode = $this->extractInformation($countryCode, self::NATIONAL_BANK_CODE);
        $this->branchCode = $this->extractInformation($countryCode, self::BRANCH_CODE);
        $this->accountNumber = $this->extractInformation($countryCode, self::ACCOUNT_NUMBER);
        $this->nationalCheckDigits = $this->extractInformation($countryCode, self::NATIONAL_CHECK_DIGITS);

        if (is_null($this->accountNumber)) {
            throw new IbanParseException(sprintf('No account number was found in the given IBAN "%s".', $iban));
        }

        if (is_null($this->nationalBankCode)) {
            throw new IbanParseException(sprintf('No national bank code was found in the given IBAN "%s".', $iban));
        }

        $account = new Account($this->accountNumber, $this->nationalBankCode, $this->countryCode);

        $account->setProperties([
            Account::KEY_BRANCH_CODE => $this->branchCode,
            Account::KEY_NATIONAL_CHECK_DIGITS => $this->nationalCheckDigits,
        ]);

        if ($account->getIbanCheckDigits() !== $this->ibanCheckDigits) {
            $this->lastError = 'The checksum does not match.';
            $this->valid = false;

            return true;
        }

        $this->lastError = null;
        $this->valid = true;

        return true;
    }

    /**
     * Returns the IBAN format without spaces by given country.
     *
     * @param string $country
     * @return string
     * @throws IbanParseException
     */
    private function getIbanFormat(string $country): string
    {
        if (!array_key_exists($country, IbanFormats::IBAN_FORMATS)) {
            throw new IbanParseException(sprintf('The given country "%s" is not supported yet.', $country));
        }

        return str_replace(' ', '', IbanFormats::IBAN_FORMATS[$country]);
    }

    /**
     * Checks the IBAN format if it is supported.
     *
     * @param string $ibanFormat
     * @return string
     */
    private function checkIbanFormat(string $ibanFormat): string
    {
        /* Remove country code from IBAN format */
        $ibanFormat = substr($ibanFormat, 2);

        /* Remove supported characters from IBAN format */
        $ibanFormat = str_replace(self::SUPPORTED_CODES, '', $ibanFormat);

        if ($ibanFormat === '') {
            return $ibanFormat;
        }

        $characters = str_split($ibanFormat);

        $uniqueCharacters = array_values(array_unique($characters));

        return implode('', $uniqueCharacters);
    }

    /**
     * Extracts information
     *
     * @param string $countryCode
     * @param string $code
     * @return string|null
     * @throws IbanParseException
     */
    private function extractInformation(string $countryCode, string $code): string|null
    {
        $ibanFormat = $this->getIbanFormat($countryCode);
        $checkFormat = $this->checkIbanFormat($ibanFormat);

        if ($checkFormat !== '') {
            throw new IbanParseException(sprintf('The given country "%s" is not supported yet (Unsupported iban format chars: "%s").', $countryCode, $checkFormat));
        }

        $position = strpos($ibanFormat, $code);

        if ($position === false) {
            return null;
        }

        $matches = [];
        preg_match(sprintf('~[%s]+~', $code), $ibanFormat, $matches);

        return substr($this->iban, $position, strlen((string) $matches[0]));
    }

    /**
     * Returns the iban.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getIban(): string
    {
        if (!$this->valid) {
            return $this->iban;
        }

        if (is_null($this->countryCode)) {
            return $this->iban;
        }

        $ibanFormat = $this->getIbanFormat($this->countryCode);

        if ($this->checkIbanFormat($ibanFormat)) {
            throw new IbanParseException(sprintf('The given country "%s" is not supported yet.', $this->countryCode));
        }

        foreach (self::SUPPORTED_CODES as $supportedCode) {
            $count = substr_count($ibanFormat, $supportedCode);

            if ($count <= 0) {
                continue;
            }

            $value = match ($supportedCode) {
                self::IBAN_CHECK_DIGITS => $this->getIbanCheckDigits(),
                self::NATIONAL_BANK_CODE => $this->getNationalBankCode(),
                self::ACCOUNT_NUMBER => $this->getAccountNumber(),
                self::BRANCH_CODE => $this->getBranchCode(),
                self::NATIONAL_CHECK_DIGITS => $this->getNationalCheckDigits(),
            };

            if (is_null($value)) {
                throw new IbanParseException(sprintf('The given IBAN "%s" is invalid.', $this->iban));
            }

            if (strlen($value) > $count) {
                throw new IbanParseException(sprintf('The given checksum "%s" is too long.', $value));
            }

            $value = str_pad($value, $count, '0', STR_PAD_LEFT);

            $ibanFormat = str_replace(str_repeat($supportedCode, $count), $value, $ibanFormat);
        }

        return $ibanFormat;
    }

    /**
     * Returns the formatted IBAN number.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getIbanFormatted(): string
    {
        return trim(chunk_split($this->getIban(), 4, ' '));
    }

    /**
     * Returns an error message.
     *
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Returns an error message.
     *
     * @return bool
     */
    public function hasLastError(): bool
    {
        return !is_null($this->lastError);
    }

    /**
     * Returns if the given IBAN number is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Returns the country code of the iban.
     *
     * @return string|null
     */
    public function getCountryCode(): string|null
    {
        return $this->countryCode;
    }

    /**
     * Returns the IBAN check digits of the IBAN.
     *
     * @return string|null
     */
    public function getIbanCheckDigits(): string|null
    {
        return $this->ibanCheckDigits;
    }

    /**
     * Returns the national bank code of the IBAN.
     *
     * @return string|null
     */
    public function getNationalBankCode(): string|null
    {
        return $this->nationalBankCode;
    }

    /**
     * Returns the branch code of the IBAN.
     *
     * @return string|null
     */
    public function getBranchCode(): ?string
    {
        return $this->branchCode;
    }

    /**
     * Returns the account number of the IBAN.
     *
     * @return string|null
     */
    public function getAccountNumber(): string|null
    {
        return $this->accountNumber;
    }

    /**
     * Returns the national check digits of the IBAN.
     *
     * @return string|null
     */
    public function getNationalCheckDigits(): ?string
    {
        return $this->nationalCheckDigits;
    }
}
