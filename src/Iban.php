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
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class Iban
{
    private const SUPPORTED_CODES = [
        IbanFormat::CODE_NATIONAL_BANK_CODE,
        IbanFormat::CODE_ACCOUNT_NUMBER,
        IbanFormat::CODE_NATIONAL_IDENTIFICATION_NUMBER,
        IbanFormat::CODE_IBAN_CHECK_DIGITS,
        IbanFormat::CODE_CURRENCY_CODE,
        IbanFormat::CODE_OWNER_ACCOUNT_NUMBER,
        IbanFormat::CODE_BIC_BANK_CODE,
        IbanFormat::CODE_BRANCH_CODE,
        IbanFormat::CODE_ACCOUNT_TYPE,
        IbanFormat::CODE_NATIONAL_CHECK_DIGITS,
        IbanFormat::ALWAYS_ZERO,
    ];

    private string|null $lastError = null;

    private bool $valid = false;

    private string|null $countryCode = null;


    private string|null $balanceAccountNumber = null;

    private string|null $nationalBankCode = null;

    private string|null $accountNumber = null;

    private string|null $nationalIdentificationNumber = null;

    private string|null $ibanCheckDigits = null;

    private string|null $currencyCode = null;

    private string|null $ownerAccountNumber = null;

    private string|null $bicBankCode = null;

    private string|null $branchCode = null;

    private string|null $accountType = null;

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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

        foreach ((new IbanFormat($countryCode))->getIbanFormatCodes() as $format) {
            match ($format) {
                IbanFormat::CODE_BALANCE_ACCOUNT_NUMBER => $this->balanceAccountNumber = $this->extractInformation($countryCode, IbanFormat::CODE_BALANCE_ACCOUNT_NUMBER),
                IbanFormat::CODE_NATIONAL_BANK_CODE => $this->nationalBankCode = $this->extractInformation($countryCode, IbanFormat::CODE_NATIONAL_BANK_CODE),
                IbanFormat::CODE_ACCOUNT_NUMBER => $this->accountNumber = $this->extractInformation($countryCode, IbanFormat::CODE_ACCOUNT_NUMBER),
                IbanFormat::CODE_NATIONAL_IDENTIFICATION_NUMBER => $this->nationalIdentificationNumber = $this->extractInformation($countryCode, IbanFormat::CODE_NATIONAL_IDENTIFICATION_NUMBER),
                IbanFormat::CODE_IBAN_CHECK_DIGITS => $this->ibanCheckDigits = $this->extractInformation($countryCode, IbanFormat::CODE_IBAN_CHECK_DIGITS),
                IbanFormat::CODE_CURRENCY_CODE => $this->currencyCode = $this->extractInformation($countryCode, IbanFormat::CODE_CURRENCY_CODE),
                IbanFormat::CODE_OWNER_ACCOUNT_NUMBER => $this->ownerAccountNumber = $this->extractInformation($countryCode, IbanFormat::CODE_OWNER_ACCOUNT_NUMBER),
                IbanFormat::CODE_BIC_BANK_CODE => $this->bicBankCode = $this->extractInformation($countryCode, IbanFormat::CODE_BIC_BANK_CODE),
                IbanFormat::CODE_BRANCH_CODE => $this->branchCode = $this->extractInformation($countryCode, IbanFormat::CODE_BRANCH_CODE),
                IbanFormat::CODE_ACCOUNT_TYPE => $this->accountType = $this->extractInformation($countryCode, IbanFormat::CODE_ACCOUNT_TYPE),
                IbanFormat::CODE_NATIONAL_CHECK_DIGITS => $this->nationalCheckDigits = $this->extractInformation($countryCode, IbanFormat::CODE_NATIONAL_CHECK_DIGITS),
                default => throw new IbanParseException(sprintf('The given format "%s" is not supported yet.', $format)),
            };
        }

        if (is_null($this->accountNumber)) {
            throw new IbanParseException(sprintf('No account number was found in the given IBAN "%s".', $iban));
        }

        if (is_null($this->nationalBankCode)) {
            throw new IbanParseException(sprintf('No national bank code was found in the given IBAN "%s".', $iban));
        }

        $account = new Account($this->accountNumber, $this->nationalBankCode, $this->countryCode, $this);

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
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * Returns the formatted IBAN number.
     *
     * @return string
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
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @return string|null
     */
    public function getBalanceAccountNumber(): ?string
    {
        return $this->balanceAccountNumber;
    }

    /**
     * @return string|null
     */
    public function getNationalBankCode(): ?string
    {
        return $this->nationalBankCode;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * @return string|null
     */
    public function getNationalIdentificationNumber(): ?string
    {
        return $this->nationalIdentificationNumber;
    }

    /**
     * @return string|null
     */
    public function getIbanCheckDigits(): ?string
    {
        return $this->ibanCheckDigits;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @return string|null
     */
    public function getOwnerAccountNumber(): ?string
    {
        return $this->ownerAccountNumber;
    }

    /**
     * @return string|null
     */
    public function getBicBankCode(): ?string
    {
        return $this->bicBankCode;
    }

    /**
     * @return string|null
     */
    public function getBranchCode(): ?string
    {
        return $this->branchCode;
    }

    /**
     * @return string|null
     */
    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    /**
     * @return string|null
     */
    public function getNationalCheckDigits(): ?string
    {
        return $this->nationalCheckDigits;
    }
}
