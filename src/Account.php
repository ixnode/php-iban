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
 */
final class Account
{
    private string|null $branchCode = null;

    private string|null $nationalCheckDigits = null;

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
        array $properties = []
    )
    {
        $this->setProperties($properties);
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
                IbanFormat::KEY_BRANCH_CODE => $this->setBranchCode($value),
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $this->setNationalCheckDigits($value),
                default => throw new AccountParseException(sprintf('Unknown property "%s" given.', $property)),
            };
        }

        return $this;
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
     * @return string
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIbanCheckDigits(): string
    {
        $checksum = intval(bcmod($this->getIbanRaw(), '97'));

        return str_pad(strval(98 - $checksum), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the calculated iban number.
     *
     * @return string
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIban(): string
    {
        return (new IbanFormat($this->countryCode))->getIban($this);
    }

    /**
     * Returns the formatted IBAN number.
     *
     * @return string
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function getIbanFormatted(): string
    {
        return trim(chunk_split($this->getIban(), 4, ' '));
    }

    /**
     * Returns the raw iban code with fake checksum to calculate the IBAN check digits.
     *
     * @return string
     * @throws AccountParseException
     * @throws IbanParseException
     */
    private function getIbanRaw(): string
    {
        return (new IbanFormat($this->countryCode))->getIbanRaw($this);
    }
}
