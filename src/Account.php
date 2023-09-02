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
     * Returns the checksum of the account number and bank code.
     *
     * @return string
     * @throws AccountParseException
     */
    public function getCheckDigits(): string
    {
        $ibanRaw = match($this->countryCode) {
            'AT', 'CH', 'DE', 'LI' => sprintf(
                '%s%s%s%s',
                $this->getNationalBankCode(),
                $this->getAccountNumber(),
                $this->calculateCodeNumber($this->getCountryCode()),
                self::CHECKSUM_FAKE
            ),
            'FR' => sprintf(
                '%s%s%s%s%s%s',
                $this->getNationalBankCode(),
                $this->getBranchCode(),
                $this->calculateCodeNumber($this->getAccountNumber()),
                $this->getNationalCheckDigits(),
                $this->calculateCodeNumber($this->getCountryCode()),
                self::CHECKSUM_FAKE
            ),
            default => throw new AccountParseException(sprintf('Country code "%s" is not supported.', $this->countryCode)),
        };

        $checksum = intval(bcmod($ibanRaw, '97'));

        return str_pad(strval(98 - $checksum), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the calculated iban number.
     *
     * @return string
     * @throws AccountParseException
     */
    public function getIban(): string
    {
        return match($this->countryCode) {
            'AT', 'CH', 'DE', 'LI' => sprintf(
                '%s%s%s%s',
                $this->getCountryCode(),
                $this->getCheckDigits(),
                $this->getNationalBankCode(),
                $this->getAccountNumber()
            ),
            'FR' => sprintf(
                '%s%s%s%s%s',
                $this->getCountryCode(),
                $this->getCheckDigits(),
                $this->getNationalBankCode(),
                $this->getBranchCode(),
                $this->getAccountNumber()
            ),
            default => throw new AccountParseException(sprintf('Country code "%s" is not supported.', $this->countryCode)),
        };
    }
}
