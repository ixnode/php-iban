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

use Ixnode\PhpException\Case\CaseUnsupportedException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpIban\Exception\IbanParseException;

/**
 * Class Validator
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
class Validator
{
    private Iban $iban;

    private AccountNumber|null $accountNumber = null;

    /**
     * @param Iban|AccountNumber $given
     * @throws CaseUnsupportedException
     * @throws IbanParseException
     * @throws TypeInvalidException
     */
    public function __construct(Iban|AccountNumber $given)
    {
        switch (true) {
            case $given instanceof Iban:
                $this->iban = $given;

                $accountNumber = $given->getAccountNumber();
                $bankCode = $given->getBankCode();
                $countryCode = $given->getCountryCode();

                $this->accountNumber = match (true) {
                    is_null($accountNumber), is_null($bankCode), is_null($countryCode) => null,
                    default => new AccountNumber($accountNumber, $bankCode, $countryCode),
                };
                break;

            case $given instanceof AccountNumber:
                $this->accountNumber = $given;
                $this->iban = new Iban($this->accountNumber->getIban());
                break;

            default:
                throw new CaseUnsupportedException('This case is not supported.');
        }
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->iban->isValid();
    }

    /**
     * Returns the IBAN number.
     *
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban->getIban();
    }

    /**
     * Returns the country code of given IBAN number.
     *
     * @return string|null
     */
    public function getCountryCode(): string|null
    {
        return $this->iban->getCountryCode();
    }

    /**
     * Returns the checksum of given IBAN number.
     *
     * @return string|null
     */
    public function getChecksum(): string|null
    {
        return $this->iban->getChecksum();
    }

    /**
     * Returns the last error message.
     *
     * @return string|null
     */
    public function getLastError(): string|null
    {
        return $this->iban->getLastError();
    }

    /**
     * Returns if a last error message exists.
     *
     * @return bool
     */
    public function hasLastError(): bool
    {
        return $this->iban->hasLastError();
    }

    /**
     * Returns the account number.
     *
     * @return string|null
     */
    public function getAccountNumber(): string|null
    {
        return $this->accountNumber?->getAccountNumber();
    }

    /**
     * Returns the bank code.
     *
     * @return string|null
     */
    public function getBankCode(): string|null
    {
        return $this->accountNumber?->getBankCode();
    }
}
