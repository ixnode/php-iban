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

                $accountNumber = $given->getAccountNumber();
                $bankCode = $given->getNationalBankCode();
                $countryCode = $given->getCountryCode();
                $branchCode = $given->getBranchCode();
                $nationalCheckDigits = $given->getNationalCheckDigits();

                if (is_null($accountNumber) || is_null($bankCode) || is_null($countryCode)) {
                    $this->account = null;
                    break;
                }

                $this->account = new Account($accountNumber, $bankCode, $countryCode);

                $this->account->setProperties([
                    Account::KEY_BRANCH_CODE => $branchCode,
                    Account::KEY_NATIONAL_CHECK_DIGITS => $nationalCheckDigits,
                ]);

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
     * Returns the IBAN number.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getIban(): string
    {
        return $this->iban->getIban();
    }

    /**
     * Returns the formatted IBAN number.
     *
     * @return string
     * @throws IbanParseException
     */
    public function getIbanFormatted(): string
    {
        return $this->iban->getIbanFormatted();
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
     * Returns the country code of given IBAN number.
     *
     * @param string $languageCode
     * @return string|null
     * @throws ValidatorParseException
     */
    public function getCountryName(string $languageCode = Locale::EN_GB): string|null
    {
        $countryCode = $this->getCountryCode();

        if (is_null($countryCode)) {
            return null;
        }

        if (!array_key_exists($countryCode, CountryAll::COUNTRY_NAMES)) {
            throw new ValidatorParseException(sprintf('The given country code "%s" is not supported.', $countryCode));
        }

        $countryNames = CountryAll::COUNTRY_NAMES[$countryCode];

        if (!array_key_exists($languageCode, $countryNames)) {
            throw new ValidatorParseException(sprintf('The given language code "%s" is not supported.', $languageCode));
        }

        return $countryNames[$languageCode];
    }

    /**
     * Returns the checksum of given IBAN number.
     *
     * @return string|null
     */
    public function getIbanCheckDigits(): string|null
    {
        return $this->iban->getIbanCheckDigits();
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
    public function getAccount(): string|null
    {
        return $this->account?->getAccountNumber();
    }

    /**
     * Returns the national bank code.
     *
     * @return string|null
     */
    public function getNationalBankCode(): string|null
    {
        return $this->account?->getNationalBankCode();
    }

    /**
     * Returns the branch code.
     *
     * @return string|null
     */
    public function getBranchCode(): string|null
    {
        return $this->account?->getBranchCode();
    }

    /**
     * Returns the national check digits.
     *
     * @return string|null
     */
    public function getNationalCheckDigits(): string|null
    {
        return $this->account?->getNationalCheckDigits();
    }
}
