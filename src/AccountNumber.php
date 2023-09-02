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

use Ixnode\PhpIban\Exception\AccountNumberParseException;

/**
 * Class AccountNumber
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final readonly class AccountNumber
{
    protected const CHECKSUM_FAKE = '00';

    /**
     * @param string $accountNumber
     * @param string $bankCode
     * @param string $countryCode
     */
    public function __construct(
        private string $accountNumber,
        private string $bankCode,
        private string $countryCode
    )
    {
    }

    /**
     * Returns the country abbreviation translate string.
     *
     * - DE: 1314
     * - etc.
     *
     * @param string $country
     * @return string
     */
    private function getCountryCodeNumber(string $country): string
    {
        $countryCodeNumber = '';

        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $landExplode = str_split(strtolower($country));

        foreach ($landExplode as $check) {
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
    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Returns the checksum of the account number and bank code.
     *
     * @return string
     */
    public function getChecksum(): string
    {
        $ibanRaw = sprintf('%s%s%s%s', $this->getBankCode(), $this->getAccountNumber(), $this->getCountryCodeNumber($this->getCountryCode()), self::CHECKSUM_FAKE);

        $checksum = intval(bcmod($ibanRaw, '97'));

        return str_pad(strval(98 - $checksum), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the calculated iban number.
     *
     * @return string
     */
    public function getIban(): string
    {
        return sprintf(
            '%s%s%s%s',
            $this->getCountryCode(),
            $this->getChecksum(),
            $this->getBankCode(),
            $this->getAccountNumber()
        );
    }
}
