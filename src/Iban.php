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
    private const CHECKSUM_CHAR = 'k';

    private const BANK_CODE_CHAR = 'b';

    private const ACCOUNT_NUMBER_CHAR = 'c';

    private string|null $lastError = null;

    private bool $valid = false;

    private string|null $countryCode = null;

    private string|null $checksum = null;

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
            $this->checksum = null;
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

        $format = $this->getIbanFormat($country);

        if (strlen($iban) !== strlen($format)) {
            $this->lastError = sprintf('Invalid length of IBAN given: "%s" (expected: "%s").', $iban, $format);
            $this->valid = false;

            return false;
        }

        $this->countryCode = $country;
        $this->checksum = $this->extractInformation($country, self::CHECKSUM_CHAR);
        $this->bankCode = $this->extractInformation($country, self::BANK_CODE_CHAR);
        $this->accountNumber = $this->extractInformation($country, self::ACCOUNT_NUMBER_CHAR);

        $accountNumber = new AccountNumber($this->accountNumber, $this->bankCode, $this->countryCode);

        if ($accountNumber->getChecksum() !== $this->checksum) {
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
        $format = $this->getIbanFormat($country);

        $position = strpos($format, $code);

        if ($position === false) {
            throw new TypeInvalidException($code, $format);
        }

        $matches = [];
        preg_match(sprintf('~[%s]+~', $code), $format, $matches);

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
    public function getChecksum(): string|null
    {
        return $this->checksum;
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
