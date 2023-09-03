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

namespace Ixnode\PhpIban\Tests\Unit;

use Ixnode\PhpException\Case\CaseUnsupportedException;
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;
use Ixnode\PhpIban\Iban;
use Ixnode\PhpIban\IbanFormat;
use Ixnode\PhpIban\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-02)
 * @since 0.1.0 (2023-09-02) First version.
 * @link Validator
 */
final class ValidatorTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProviderIban
     *
     * @test
     * @testdox $number) Test account number: "$accountNumber"
     * @param int $number
     * @param Iban $given
     * @param bool $valid
     * @param string|null $lastError
     * @param array<string, mixed> $expected
     * @throws AccountParseException
     * @throws CaseUnsupportedException
     * @throws IbanParseException
     */
    public function wrapperIban(
        int $number,
        Iban $given,
        bool $valid,
        string|null $lastError,
        array $expected,
    ): void
    {
        /* Arrange */

        /* Act */
        $validator = new Validator($given);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertSame($validator->isValid(), $valid);
        $this->assertSame($validator->getLastError(), $lastError);

        $this->assertSame([
            IbanFormat::KEY_COUNTRY_CODE,
            IbanFormat::KEY_IBAN_CHECK_DIGITS,
            IbanFormat::KEY_NATIONAL_BANK_CODE,
            IbanFormat::KEY_ACCOUNT_NUMBER,
            IbanFormat::KEY_BRANCH_CODE,
            IbanFormat::KEY_NATIONAL_CHECK_DIGITS,
            IbanFormat::KEY_IBAN_FORMATTED,
        ], array_keys($expected));

        $iban = $validator->getIban();
        $account = $validator->getAccount();

        $this->assertSame($expected, [
            IbanFormat::KEY_COUNTRY_CODE => $iban->getCountryCode(),
            IbanFormat::KEY_IBAN_CHECK_DIGITS => $iban->getIbanCheckDigits(),
            IbanFormat::KEY_NATIONAL_BANK_CODE => $account?->getNationalBankCode(),
            IbanFormat::KEY_ACCOUNT_NUMBER => $account?->getAccountNumber(),
            IbanFormat::KEY_BRANCH_CODE => $account?->getBranchCode(),
            IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $account?->getNationalCheckDigits(),
            IbanFormat::KEY_IBAN_FORMATTED => $iban->getIbanFormatted(),
        ]);
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, mixed>>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderIban(): array
    {
        $number = 0;

        return [

            /**
             * AT, CH, DE, LI: Simple IBAN validator test (positive true tests).
             *
             * @see [AT,CH,DE,LI] https://ibanvalidieren.de/beispiele.html
             */
            [++$number, new Iban('AT026000000001349870'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AT',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '60000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00001349870',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AT02 6000 0000 0134 9870',
            ]],
            [++$number, new Iban('CH0209000000100013997'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'CH',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '09000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000100013997',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'CH02 0900 0000 1000 1399 7',
            ]],
            [++$number, new Iban('DE02120300000000202051'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'DE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '12030000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000202051',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('LI0208800000017197386'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'LI',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '08800',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000017197386',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'LI02 0880 0000 0171 9738 6',
            ]],

            /**
             * AL: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('AL35202111090000000001234567'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AL',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '35',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '202',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000000001234567',
                IbanFormat::KEY_BRANCH_CODE => '1110',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '9',
                IbanFormat::KEY_IBAN_FORMATTED => 'AL35 2021 1109 0000 0000 0123 4567',
            ]],

            /**
             * AD: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('AD1400080001001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AD',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '14',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '0008',
                IbanFormat::KEY_ACCOUNT_NUMBER => '001234567890',
                IbanFormat::KEY_BRANCH_CODE => '0001',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AD14 0008 0001 0012 3456 7890',
            ]],

            /**
             * AZ: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('AZ96AZEJ00000000001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AZ',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '96',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'AZEJ',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00000000001234567890',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AZ96 AZEJ 0000 0000 0012 3456 7890',
            ]],

            /**
             * ES: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('ES9121000418450200051332'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'ES',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '91',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '2100',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0200051332',
                IbanFormat::KEY_BRANCH_CODE => '0418',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '45',
                IbanFormat::KEY_IBAN_FORMATTED => 'ES91 2100 0418 4502 0005 1332',
            ]],
            [++$number, new Iban('ES6720310000010118272402'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'ES',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '67',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '2031',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0118272402',
                IbanFormat::KEY_BRANCH_CODE => '0000',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '01',
                IbanFormat::KEY_IBAN_FORMATTED => 'ES67 2031 0000 0101 1827 2402',
            ]],

            /**
             * FR: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('FR1420041010050500013M02606'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '14',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '20041',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0500013M026',
                IbanFormat::KEY_BRANCH_CODE => '01005',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '06',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR14 2004 1010 0505 0001 3M02 606',
            ]],
            [++$number, new Iban('FR7630027175330002005370159'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '76',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30027',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00020053701',
                IbanFormat::KEY_BRANCH_CODE => '17533',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '59',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR76 3002 7175 3300 0200 5370 159',
            ]],
            [++$number, new Iban('FR7630006000011234567890189'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '76',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30006',
                IbanFormat::KEY_ACCOUNT_NUMBER => '12345678901',
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '89',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR76 3000 6000 0112 3456 7890 189',
            ]],

            /**
             * PT: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('PT50003600409911001102673'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'PT',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '50',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '0036',
                IbanFormat::KEY_ACCOUNT_NUMBER => '99110011026',
                IbanFormat::KEY_BRANCH_CODE => '0040',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '73',
                IbanFormat::KEY_IBAN_FORMATTED => 'PT50 0036 0040 9911 0011 0267 3',
            ]],

            /**
             * TR: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             * @see https://de.iban.com/struktur
             */
            [++$number, new Iban('TR320010009999901234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'TR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '32',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '00100',
                IbanFormat::KEY_ACCOUNT_NUMBER => '9999901234567890',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'TR32 0010 0099 9990 1234 5678 90',
            ]],

            /**
             * Wrong checksum (positive false tests).
             */
            [++$number, new Iban('DE03120300000000202051'), false, 'The checksum does not match.', [
                IbanFormat::KEY_COUNTRY_CODE => 'DE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '03',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '12030000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000202051',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE03 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('FR7530006000011234567890189'), false, 'The checksum does not match.', [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '75',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30006',
                IbanFormat::KEY_ACCOUNT_NUMBER => '12345678901',
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '89',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR75 3000 6000 0112 3456 7890 189',
            ]],

            /**
             * Wrong length (positive false tests).
             */
            [++$number, new Iban('DE0212030000000020205'), false, 'Invalid length of IBAN given: "DE0212030000000020205" (expected: "DEkkbbbbbbbbcccccccccc").', [
                IbanFormat::KEY_COUNTRY_CODE => null,
                IbanFormat::KEY_IBAN_CHECK_DIGITS => null,
                IbanFormat::KEY_NATIONAL_BANK_CODE => null,
                IbanFormat::KEY_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 5',
            ]],
            [++$number, new Iban('DE021203000000002020512'), false, 'Invalid length of IBAN given: "DE021203000000002020512" (expected: "DEkkbbbbbbbbcccccccccc").', [
                IbanFormat::KEY_COUNTRY_CODE => null,
                IbanFormat::KEY_IBAN_CHECK_DIGITS => null,
                IbanFormat::KEY_NATIONAL_BANK_CODE => null,
                IbanFormat::KEY_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 512',
            ]],

            /**
             * Wrong country (positive false tests).
             */
            [++$number, new Iban('XX02120300000000202051'), false, 'The given country "XX" is not supported yet.', [
                IbanFormat::KEY_COUNTRY_CODE => null,
                IbanFormat::KEY_IBAN_CHECK_DIGITS => null,
                IbanFormat::KEY_NATIONAL_BANK_CODE => null,
                IbanFormat::KEY_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'XX02 1203 0000 0000 2020 51',
            ]],
        ];
    }
}
