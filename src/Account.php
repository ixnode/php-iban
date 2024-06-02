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

use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;
use Ixnode\PhpIban\Exception\ValidatorParseException;
use Ixnode\PhpTimezone\Constants\CountryAll;
use Ixnode\PhpTimezone\Constants\Locale;

/**
 * Class AccountNumber
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Account
{
    private string|null $balanceAccountNumber = null;

    private string|null $nationalIdentificationNumber = null;

    private string|null $currencyCode = null;

    private string|null $ownerAccountNumber = null;

    private string|null $accountNumberPrefix = null;

    private string|null $bicBankCode = null;

    private string|null $branchCode = null;

    private string|null $accountType = null;

    private string|null $nationalCheckDigits = null;

    private string|null $lastError = null;

    /**
     * @param string $accountNumber
     * @param string $nationalBankCode
     * @param string $countryCode
     * @param array<string, mixed> $properties
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function __construct(
        private readonly string $accountNumber,
        private readonly string $nationalBankCode,
        private readonly string $countryCode,
        array|Iban $properties = []
    )
    {
        match (true) {
            is_array($properties) => $this->setProperties($properties),
            $properties instanceof Iban => $this->setPropertiesFromIban($properties),
        };
    }

    /**
     * @param Iban $iban
     * @return void
     * @throws AccountParseException
     * @throws IbanParseException
     */
    private function setPropertiesFromIban(Iban $iban): void
    {
        $ibanFormat = new IbanFormat($this->countryCode);

        $ignoredProperties = [
            IbanFormat::KEY_NATIONAL_BANK_CODE,
            IbanFormat::KEY_ACCOUNT_NUMBER,
        ];

        $availableProperties = $ibanFormat->getIbanFormatPropertyKeysAll($ignoredProperties);
        $expectedProperties = $ibanFormat->getIbanFormatPropertyKeys($ignoredProperties);

        $missingProperties = [];
        $unknownProperties = [];

        foreach ($availableProperties as $property) {
            $value = match ($property) {
                IbanFormat::KEY_NATIONAL_BANK_CODE => $iban->getNationalBankCode(),
                IbanFormat::KEY_ACCOUNT_NUMBER => $iban->getAccountNumber(),
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => $iban->getBalanceAccountNumber(),
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => $iban->getNationalIdentificationNumber(),
                IbanFormat::KEY_CURRENCY_CODE => $iban->getCurrencyCode(),
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => $iban->getOwnerAccountNumber(),
                IbanFormat::KEY_ACCOUNT_NUMBER_PREFIX => $iban->getAccountNumberPrefix(),
                IbanFormat::KEY_BIC_BANK_CODE => $iban->getBicBankCode(),
                IbanFormat::KEY_BRANCH_CODE => $iban->getBranchCode(),
                IbanFormat::KEY_ACCOUNT_TYPE => $iban->getAccountType(),
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $iban->getNationalCheckDigits(),
                default => throw new AccountParseException(sprintf('Unknown property "%s".', $property)),
            };

            match (true) {
                in_array($property, $expectedProperties) && is_null($value) => $missingProperties[] = $property,
                !in_array($property, $expectedProperties) && !is_null($value) => $unknownProperties[] = $property,
                default => null,
            };
        }

        if (count($missingProperties) > 0) {
            throw new AccountParseException(sprintf('Missing properties: %s', implode(', ', $missingProperties)));
        }

        if (count($unknownProperties) > 0) {
            throw new AccountParseException(sprintf('Unknown properties: %s', implode(', ', $unknownProperties)));
        }

        foreach ($expectedProperties as $property) {
            match ($property) {
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => $this->setBalanceAccountNumber($iban->getBalanceAccountNumber()),
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => $this->setNationalIdentificationNumber($iban->getNationalIdentificationNumber()),
                IbanFormat::KEY_CURRENCY_CODE => $this->setCurrencyCode($iban->getCurrencyCode()),
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => $this->setOwnerAccountNumber($iban->getOwnerAccountNumber()),
                IbanFormat::KEY_ACCOUNT_NUMBER_PREFIX => $this->setAccountNumberPrefix($iban->getAccountNumberPrefix()),
                IbanFormat::KEY_BIC_BANK_CODE => $this->setBicBankCode($iban->getBicBankCode()),
                IbanFormat::KEY_BRANCH_CODE => $this->setBranchCode($iban->getBranchCode()),
                IbanFormat::KEY_ACCOUNT_TYPE => $this->setAccountType($iban->getAccountType()),
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $this->setNationalCheckDigits($iban->getNationalCheckDigits()),
                default => throw new AccountParseException(sprintf('Unknown property "%s".', $property)),
            };
        }
    }

    /**
     * Sets some properties.
     *
     * @param array<string, mixed> $givenProperties
     * @return $this
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function setProperties(array $givenProperties): self
    {
        $expectedProperties = (new IbanFormat($this->countryCode))->getIbanFormatPropertyKeys([
            IbanFormat::KEY_NATIONAL_BANK_CODE,
            IbanFormat::KEY_ACCOUNT_NUMBER,
        ]);

        $unknownProperties = array_diff(array_keys($givenProperties), $expectedProperties);
        $missingProperties = array_diff($expectedProperties, array_keys($givenProperties));

        if (count($missingProperties) > 0) {
            throw new AccountParseException(sprintf('Missing properties: %s', implode(', ', $missingProperties)));
        }

        if (count($unknownProperties) > 0) {
            throw new AccountParseException(sprintf('Unknown properties: %s', implode(', ', $unknownProperties)));
        }

        foreach ($givenProperties as $property => $value) {
            if (!is_null($value) && !is_string($value)) {
                throw new AccountParseException(sprintf('Property "%s" must be a string or null.', $property));
            }

            match ($property) {
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => $this->setBalanceAccountNumber($value),
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => $this->setNationalIdentificationNumber($value),
                IbanFormat::KEY_CURRENCY_CODE => $this->setCurrencyCode($value),
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => $this->setOwnerAccountNumber($value),
                IbanFormat::KEY_ACCOUNT_NUMBER_PREFIX => $this->setAccountNumberPrefix($value),
                IbanFormat::KEY_BIC_BANK_CODE => $this->setBicBankCode($value),
                IbanFormat::KEY_BRANCH_CODE => $this->setBranchCode($value),
                IbanFormat::KEY_ACCOUNT_TYPE => $this->setAccountType($value),
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $this->setNationalCheckDigits($value),
                default => throw new AccountParseException(sprintf('Unknown property "%s".', $property)),
            };
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Returns the country name of the IBAN.
     *
     * @param string $languageCode
     * @return string|null
     * @throws ValidatorParseException
     */
    public function getCountryName(string $languageCode = Locale::EN_GB): string|null
    {
        if (!array_key_exists($this->countryCode, CountryAll::COUNTRY_NAMES)) {
            throw new ValidatorParseException(sprintf('The given country code "%s" is not supported.', $this->countryCode));
        }

        $countryNames = CountryAll::COUNTRY_NAMES[$this->countryCode];

        if (!array_key_exists($languageCode, $countryNames)) {
            throw new ValidatorParseException(sprintf('The given language code "%s" is not supported.', $languageCode));
        }

        return $countryNames[$languageCode];
    }

    /**
     * Returns the account number.
     *
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * Returns the bank code.
     *
     * @return string
     */
    public function getNationalBankCode(): string
    {
        return $this->nationalBankCode;
    }

    /**
     * @return string|null
     */
    public function getBalanceAccountNumber(): ?string
    {
        return $this->balanceAccountNumber;
    }

    /**
     * @param string|null $balanceAccountNumber
     * @return self
     */
    public function setBalanceAccountNumber(?string $balanceAccountNumber): self
    {
        $this->balanceAccountNumber = $balanceAccountNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNationalIdentificationNumber(): ?string
    {
        return $this->nationalIdentificationNumber;
    }

    /**
     * @param string|null $nationalIdentificationNumber
     * @return self
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function setNationalIdentificationNumber(?string $nationalIdentificationNumber): self
    {
        $this->nationalIdentificationNumber = $nationalIdentificationNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @param string|null $currencyCode
     * @return self
     */
    public function setCurrencyCode(?string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOwnerAccountNumber(): ?string
    {
        return $this->ownerAccountNumber;
    }

    /**
     * @param string|null $ownerAccountNumber
     * @return self
     */
    public function setOwnerAccountNumber(?string $ownerAccountNumber): self
    {
        $this->ownerAccountNumber = $ownerAccountNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccountNumberPrefix(): ?string
    {
        return $this->accountNumberPrefix;
    }

    /**
     * @param string|null $accountNumberPrefix
     * @return self
     */
    public function setAccountNumberPrefix(?string $accountNumberPrefix): self
    {
        $this->accountNumberPrefix = $accountNumberPrefix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBicBankCode(): ?string
    {
        return $this->bicBankCode;
    }

    /**
     * @param string|null $bicBankCode
     * @return self
     */
    public function setBicBankCode(?string $bicBankCode): self
    {
        $this->bicBankCode = $bicBankCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBranchCode(): ?string
    {
        return $this->branchCode;
    }

    /**
     * @param string|null $branchCode
     * @return self
     */
    public function setBranchCode(?string $branchCode): self
    {
        $this->branchCode = $branchCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    /**
     * @param string|null $accountType
     * @return self
     */
    public function setAccountType(?string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNationalCheckDigits(): ?string
    {
        return $this->nationalCheckDigits;
    }

    /**
     * @param string|null $nationalCheckDigits
     * @return self
     */
    public function setNationalCheckDigits(?string $nationalCheckDigits): self
    {
        $this->nationalCheckDigits = $nationalCheckDigits;

        return $this;
    }

    /**
     * Returns the IBAN check digits of the account number and bank code.
     *
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIbanCheckDigits(): string|null
    {
        $ibanRaw = $this->getIbanRaw();

        if (is_null($ibanRaw)) {
            return null;
        }

        $checksum = intval(bcmod($ibanRaw, '97'));

        return str_pad(strval(98 - $checksum), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the calculated iban number.
     *
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIban(): string|null
    {
        $ibanFormat = new IbanFormat($this->countryCode);

        $iban = $ibanFormat->getIban($this);

        if ($ibanFormat->hasLastError()) {
            $this->setLastError($ibanFormat->getLastError());
        }

        return $iban;
    }

    /**
     * Returns the formatted IBAN number.
     *
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIbanFormatted(): string|null
    {
        $iban = $this->getIban();

        if (is_null($iban)) {
            return null;
        }

        return trim(chunk_split($iban, 4, ' '));
    }

    /**
     * Returns the raw iban code with fake checksum to calculate the IBAN check digits.
     *
     * @return string|null
     * @throws AccountParseException
     * @throws IbanParseException
     */
    private function getIbanRaw(): string|null
    {
        $ibanFormat = new IbanFormat($this->countryCode);

        $ibanRaw = $ibanFormat->getIbanRaw($this);

        if ($ibanFormat->hasLastError()) {
            $this->setLastError($ibanFormat->getLastError());
        }

        return $ibanRaw;
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
