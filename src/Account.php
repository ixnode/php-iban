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

use Ixnode\PhpException\Parser\ParserException;
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpTimezone\Constants\CountryEurope;

/**
 * Class AccountNumber
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final class Account
{
    protected const CHECKSUM_FAKE = '00';

    private string|null $branchCode = null;

    private string|null $nationalCheckDigits = null;

    final public const KEY_BRANCH_CODE = 'branch-code';

    final public const KEY_NATIONAL_CHECK_DIGITS = 'national-check-digits';

    /**
     * @param string $accountNumber
     * @param string $nationalBankCode
     * @param string $countryCode
     */
    public function __construct(
        private readonly string $accountNumber,
        private readonly string $nationalBankCode,
        private readonly string $countryCode
    )
    {
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
     * Sets some properties.
     *
     * @param array<string, mixed> $properties
     * @return $this
     * @throws AccountParseException
     */
    public function setProperties(array $properties): self
    {
        foreach ($properties as $property => $value) {
            if (!is_null($value) && !is_string($value)) {
                throw new AccountParseException(sprintf('Property "%s" must be a string or null.', $property));
            }

            match ($property) {
                self::KEY_BRANCH_CODE => $this->setBranchCode($value),
                self::KEY_NATIONAL_CHECK_DIGITS => $this->setNationalCheckDigits($value),
                default => throw new AccountParseException(sprintf('Unknown property "%s" given.', $property)),
            };
        }

        return $this;
    }

    /**
     * Returns the IBAN check digits of the account number and bank code.
     *
     * @return string
     * @throws AccountParseException
     */
    public function getIbanCheckDigits(): string
    {
        $checksum = intval(bcmod($this->getIbanRaw(), '97'));

        return str_pad(strval(98 - $checksum), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the short IBAN without country code and IBAN check digits.
     *
     * @return string
     * @throws AccountParseException
     */
    private function getIbanShort(): string
    {
        return match($this->countryCode) {
            CountryEurope::COUNTRY_CODE_AT,
            CountryEurope::COUNTRY_CODE_CH,
            CountryEurope::COUNTRY_CODE_DE,
            CountryEurope::COUNTRY_CODE_LI => sprintf(
                '%s%s',
                $this->getNationalBankCode(),
                $this->getAccountNumber()
            ),
            CountryEurope::COUNTRY_CODE_FR => sprintf(
                '%s%s%s%s',
                $this->getNationalBankCode(),
                $this->getBranchCode(),
                $this->getAccountNumber(),
                $this->getNationalCheckDigits()
            ),
            default => throw new AccountParseException(sprintf('Country code "%s" is not supported.', $this->countryCode)),
        };
    }

    /**
     * Returns the calculated iban number.
     *
     * @return string
     * @throws AccountParseException
     */
    public function getIban(): string
    {
        return sprintf('%s%s%s', $this->getCountryCode(), $this->getIbanCheckDigits(), $this->getIbanShort());
    }

    /**
     * Returns the raw iban code with fake checksum to calculate the IBAN check digits.
     *
     * @return string
     * @throws AccountParseException
     */
    private function getIbanRaw(): string
    {
        return sprintf('%s%s%s', $this->calculateCodeNumber($this->getIbanShort()), $this->calculateCodeNumber($this->getCountryCode()), self::CHECKSUM_FAKE);
    }
}
