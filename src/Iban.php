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

use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpIban\Constant\IbanFormats;
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
    /** @phpstan-ignore-next-line */
    private const BRANCH_CODE = 's';

    /* Account type */
    /** @phpstan-ignore-next-line */
    private const ACCOUNT_TYPE = 't';

    /* National check digits */
    /** @phpstan-ignore-next-line */
    private const NATIONAL_CHECK_DIGITS = 'x';

    /* Always zero */
    /** @phpstan-ignore-next-line */
    private const ALWAYS_ZERO = '0';

    private string|null $lastError = null;

    private bool $valid = false;

    private string|null $countryCode = null;

    private string|null $ibanCheckDigits = null;

    private string|null $bankCode = null;

    private string|null $accountNumber = null;

    /**
     * @param string $iban
     * @throws IbanParseException
     * @throws TypeInvalidException
     */
    public function __construct(private readonly string $iban)
    {
        if (!$this->parseIban()) {
            $this->countryCode = null;
            $this->ibanCheckDigits = null;
            $this->bankCode = null;
            $this->accountNumber = null;
        }
    }

    /**
     * Parses the given IBAN number.
     *
     * @return bool
     * @throws IbanParseException
     * @throws TypeInvalidException
     */
    private function parseIban(): bool
    {
        $iban = $this->iban;
        $country = strtoupper(substr($iban, 0, 2));

        if (!array_key_exists($country, IbanFormats::IBAN_FORMATS)) {
            $this->lastError = sprintf('The given country "%s" is not supported yet.', $country);
            $this->valid = false;

            return false;
        }

        $ibanFormat = $this->getIbanFormat($country);

        if ($this->checkIbanFormat($ibanFormat) !== '') {
            $this->lastError = sprintf('The given country "%s" is not supported yet (Unsupported iban format char).', $country);
            $this->valid = false;

            return false;
        }

        if (strlen($iban) !== strlen($ibanFormat)) {
            $this->lastError = sprintf('Invalid length of IBAN given: "%s" (expected: "%s").', $iban, $ibanFormat);
            $this->valid = false;

            return false;
        }

        $this->countryCode = $country;
        $this->ibanCheckDigits = $this->extractInformation($country, self::IBAN_CHECK_DIGITS);
        $this->bankCode = $this->extractInformation($country, self::NATIONAL_BANK_CODE);
        $this->accountNumber = $this->extractInformation($country, self::ACCOUNT_NUMBER);

        $accountNumber = new AccountNumber($this->accountNumber, $this->bankCode, $this->countryCode);

        if ($accountNumber->getChecksum() !== $this->ibanCheckDigits) {
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
        return str_replace(
            [
                self::NATIONAL_BANK_CODE,
                self::ACCOUNT_NUMBER,
                self::IBAN_CHECK_DIGITS,
            ],
            '',
            $ibanFormat
        );
    }

    /**
     * Extracts information
     *
     * @param string $country
     * @param string $code
     * @return string
     * @throws TypeInvalidException
     * @throws IbanParseException
     */
    private function extractInformation(string $country, string $code): string
    {
        $ibanFormat = $this->getIbanFormat($country);

        if ($this->checkIbanFormat($ibanFormat) !== '') {
            throw new IbanParseException(sprintf('The given country "%s" is not supported yet.', $country));
        }

        $position = strpos($ibanFormat, $code);

        if ($position === false) {
            throw new TypeInvalidException($code, $ibanFormat);
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
     * Returns the country of the iban.
     *
     * @return string|null
     */
    public function getCountryCode(): string|null
    {
        return $this->countryCode;
    }

    /**
     * @return string|null
     */
    public function getIbanCheckDigits(): string|null
    {
        return $this->ibanCheckDigits;
    }

    /**
     * @return string|null
     */
    public function getBankCode(): string|null
    {
        return $this->bankCode;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): string|null
    {
        return $this->accountNumber;
    }
}
