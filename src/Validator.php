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
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;
use Ixnode\PhpIban\Exception\ValidatorParseException;
use Ixnode\PhpTimezone\Constants\CountryAll;
use Ixnode\PhpTimezone\Constants\Locale;

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

    private Account|null $account = null;

    /**
     * @param Iban|Account $given
     * @throws CaseUnsupportedException
     * @throws IbanParseException
     * @throws AccountParseException
     */
    public function __construct(Iban|Account $given)
    {
        switch (true) {
            case $given instanceof Iban:
                $this->iban = $given;

                $countryCode = $given->getCountryCode();
                $bankCode = $given->getNationalBankCode();
                $accountNumber = $given->getAccountNumber();

                if (is_null($countryCode) || is_null($bankCode) || is_null($accountNumber)) {
                    $this->account = null;
                    break;
                }

                $this->account = new Account($accountNumber, $bankCode, $countryCode, $this->iban);
                break;

            case $given instanceof Account:
                $this->account = $given;
                $this->iban = new Iban($this->account->getIban());
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
     * Returns IBAN container.
     *
     * @return Iban
     */
    public function getIban(): Iban
    {
        return $this->iban;
    }

    /**
     * Returns the account container.
     *
     * @return Account|null
     */
    public function getAccount(): Account|null
    {
        return $this->account;
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
}
