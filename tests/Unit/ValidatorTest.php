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
     * @dataProvider dataProviderIbanCountries
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
            IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER,
            IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER,
            IbanFormat::KEY_CURRENCY_CODE,
            IbanFormat::KEY_OWNER_ACCOUNT_NUMBER,
            IbanFormat::KEY_BIC_BANK_CODE,
            IbanFormat::KEY_BRANCH_CODE,
            IbanFormat::KEY_ACCOUNT_TYPE,
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
            IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => $account?->getBalanceAccountNumber(),
            IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => $account?->getNationalIdentificationNumber(),
            IbanFormat::KEY_CURRENCY_CODE => $account?->getCurrencyCode(),
            IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => $account?->getOwnerAccountNumber(),
            IbanFormat::KEY_BIC_BANK_CODE => $account?->getBicBankCode(),
            IbanFormat::KEY_BRANCH_CODE => $account?->getBranchCode(),
            IbanFormat::KEY_ACCOUNT_TYPE => $account?->getAccountType(),
            IbanFormat::KEY_NATIONAL_CHECK_DIGITS => $account?->getNationalCheckDigits(),
            IbanFormat::KEY_IBAN_FORMATTED => $iban->getIbanFormatted(),
        ]);
    }

    /**
     * Data provider.
     *
     * https://wise.com/de/iban/checker
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
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AT02 6000 0000 0134 9870',
            ]],
            [++$number, new Iban('CH0209000000100013997'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'CH',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '09000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000100013997',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'CH02 0900 0000 1000 1399 7',
            ]],
            [++$number, new Iban('DE02120300000000202051'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'DE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '12030000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000202051',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('LI0208800000017197386'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'LI',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '08800',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000017197386',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'LI02 0880 0000 0171 9738 6',
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
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0418',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '45',
                IbanFormat::KEY_IBAN_FORMATTED => 'ES91 2100 0418 4502 0005 1332',
            ]],
            [++$number, new Iban('ES6720310000010118272402'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'ES',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '67',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '2031',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0118272402',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0000',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
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
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '01005',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '06',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR14 2004 1010 0505 0001 3M02 606',
            ]],
            [++$number, new Iban('FR7630027175330002005370159'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '76',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30027',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00020053701',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '17533',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '59',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR76 3002 7175 3300 0200 5370 159',
            ]],
            [++$number, new Iban('FR7630006000011234567890189'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '76',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30006',
                IbanFormat::KEY_ACCOUNT_NUMBER => '12345678901',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '89',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR76 3000 6000 0112 3456 7890 189',
            ]],

            /**
             * Wrong checksum (positive false tests).
             */
            [++$number, new Iban('DE03120300000000202051'), false, 'The checksum does not match.', [
                IbanFormat::KEY_COUNTRY_CODE => 'DE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '03',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '12030000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000202051',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE03 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('FR7530006000011234567890189'), false, 'The checksum does not match.', [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '75',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '30006',
                IbanFormat::KEY_ACCOUNT_NUMBER => '12345678901',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
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
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 5',
            ]],
            [++$number, new Iban('DE021203000000002020512'), false, 'Invalid length of IBAN given: "DE021203000000002020512" (expected: "DEkkbbbbbbbbcccccccccc").', [
                IbanFormat::KEY_COUNTRY_CODE => null,
                IbanFormat::KEY_IBAN_CHECK_DIGITS => null,
                IbanFormat::KEY_NATIONAL_BANK_CODE => null,
                IbanFormat::KEY_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
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
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'XX02 1203 0000 0000 2020 51',
            ]],
        ];
    }

    /**
     * Data provider.
     *
     * https://wise.com/de/iban/checker
     *
     * @return array<int, array<int, mixed>>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderIbanCountries(): array
    {
        $number = 0;

        return [
            [++$number, new Iban('AD1400080001001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AD',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '14',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '0008',
                IbanFormat::KEY_ACCOUNT_NUMBER => '001234567890',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0001',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AD14 0008 0001 0012 3456 7890',
            ]],
            [++$number, new Iban('AL35202111090000000001234567'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AL',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '35',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '202',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000000001234567',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '1110',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '9',
                IbanFormat::KEY_IBAN_FORMATTED => 'AL35 2021 1109 0000 0000 0123 4567',
            ]],
            [++$number, new Iban('AT026000000001349870'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AT',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '60000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00001349870',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AT02 6000 0000 0134 9870',
            ]],
            [++$number, new Iban('AZ96AZEJ00000000001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'AZ',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '96',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'AZEJ',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00000000001234567890',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'AZ96 AZEJ 0000 0000 0012 3456 7890',
            ]],
            [++$number, new Iban('BA393385804800211234'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'BA',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '39',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '338',
                IbanFormat::KEY_ACCOUNT_NUMBER => '48002112',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '580',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '34',
                IbanFormat::KEY_IBAN_FORMATTED => 'BA39 3385 8048 0021 1234',
            ]],
            [++$number, new Iban('BE71096123456769'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'BE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '71',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '096',
                IbanFormat::KEY_ACCOUNT_NUMBER => '1234567',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '69',
                IbanFormat::KEY_IBAN_FORMATTED => 'BE71 0961 2345 6769',
            ]],
            [++$number, new Iban('BH02CITI00001077181611'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'BH',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'CITI',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00001077181611',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'BH02 CITI 0000 1077 1816 11',
            ]],
            [++$number, new Iban('BG18RZBB91550123456789'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'BG',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '18',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'RZBB',
                IbanFormat::KEY_ACCOUNT_NUMBER => '23456789',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '9155',
                IbanFormat::KEY_ACCOUNT_TYPE => '01',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'BG18 RZBB 9155 0123 4567 89',
            ]],
            [++$number, new Iban('BR1500000000000010932840814P2'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'BR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '15',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '00000000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0932840814',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => '2',
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_ACCOUNT_TYPE => 'P',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'BR15 0000 0000 0000 1093 2840 814P 2',
            ]],
            [++$number, new Iban('CH0209000000100013997'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'CH',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '09000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000100013997',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'CH02 0900 0000 1000 1399 7',
            ]],
            [++$number, new Iban('CR23015108410026012345'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'CR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '23',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '151',
                IbanFormat::KEY_ACCOUNT_NUMBER => '08410026012345',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'CR23 0151 0841 0026 0123 45',
            ]],
            [++$number, new Iban('DE02120300000000202051'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'DE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '12030000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000202051',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('DK9520000123456789'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'DK',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '95',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '2000',
                IbanFormat::KEY_ACCOUNT_NUMBER => '012345678',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '9',
                IbanFormat::KEY_IBAN_FORMATTED => 'DK95 2000 0123 4567 89',
            ]],
            [++$number, new Iban('DO28BAGR00000001212453611324'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'DO',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '28',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'BAGR',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00000001212453611324',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'DO28 BAGR 0000 0001 2124 5361 1324',
            ]],
            [++$number, new Iban('ES9121000418450200051332'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'ES',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '91',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '2100',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0200051332',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0418',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '45',
                IbanFormat::KEY_IBAN_FORMATTED => 'ES91 2100 0418 4502 0005 1332',
            ]],
            [++$number, new Iban('EE471000001020145685'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'EE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '47',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '10',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00102014568',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '00',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '5',
                IbanFormat::KEY_IBAN_FORMATTED => 'EE47 1000 0010 2014 5685',
            ]],
            [++$number, new Iban('FI1410093000123458'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FI',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '14',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '100930',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0012345',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '8',
                IbanFormat::KEY_IBAN_FORMATTED => 'FI14 1009 3000 1234 58',
            ]],
            [++$number, new Iban('FO9264600123456789'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FO',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '92',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '6460',
                IbanFormat::KEY_ACCOUNT_NUMBER => '012345678',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '9',
                IbanFormat::KEY_IBAN_FORMATTED => 'FO92 6460 0123 4567 89',
            ]],
            [++$number, new Iban('FR1420041010050500013M02606'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'FR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '14',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '20041',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0500013M026',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '01005',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '06',
                IbanFormat::KEY_IBAN_FORMATTED => 'FR14 2004 1010 0505 0001 3M02 606',
            ]],
            [++$number, new Iban('GE60NB0000000123456789'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'GE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '60',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'NB',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000000123456789',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'GE60 NB00 0000 0123 4567 89',
            ]],
            [++$number, new Iban('GI75NWBK000000007099453'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'GI',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '75',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'NWBK',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000000007099453',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'GI75 NWBK 0000 0000 7099 453',
            ]],
            [++$number, new Iban('GL8964710123456789'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'GL',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '89',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '6471',
                IbanFormat::KEY_ACCOUNT_NUMBER => '012345678',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '9',
                IbanFormat::KEY_IBAN_FORMATTED => 'GL89 6471 0123 4567 89',
            ]],
            [++$number, new Iban('GR9608100010000001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'GR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '96',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '081',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000001234567890',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0001',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'GR96 0810 0010 0000 0123 4567 890',
            ]],
            [++$number, new Iban('GT20AGRO00000000001234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'GT',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '20',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'AGRO',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000001234567890',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => '00',
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => '00',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'GT20 AGRO 0000 0000 0012 3456 7890',
            ]],
            [++$number, new Iban('IE64IRCE92050112345678'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'IE',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '64',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '920501',
                IbanFormat::KEY_ACCOUNT_NUMBER => '12345678',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => 'IRCE',
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'IE64 IRCE 9205 0112 3456 78',
            ]],
            [++$number, new Iban('IQ20CBIQ861800101010500'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'IQ',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '20',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'CBIQ',
                IbanFormat::KEY_ACCOUNT_NUMBER => '800101010500',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '861',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'IQ20 CBIQ 8618 0010 1010 500',
            ]],
            [++$number, new Iban('LI0208800000017197386'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'LI',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '02',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '08800',
                IbanFormat::KEY_ACCOUNT_NUMBER => '000017197386',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'LI02 0880 0000 0171 9738 6',
            ]],
            [++$number, new Iban('PT50003600409911001102673'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'PT',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '50',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '0036',
                IbanFormat::KEY_ACCOUNT_NUMBER => '99110011026',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => '0040',
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '73',
                IbanFormat::KEY_IBAN_FORMATTED => 'PT50 0036 0040 9911 0011 0267 3',
            ]],
            [++$number, new Iban('SV43ACAT00000000000000123123'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'SV',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '43',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'ACAT',
                IbanFormat::KEY_ACCOUNT_NUMBER => '00000000000000123123',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'SV43 ACAT 0000 0000 0000 0012 3123',
            ]],
            [++$number, new Iban('TR320010009999901234567890'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'TR',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '32',
                IbanFormat::KEY_NATIONAL_BANK_CODE => '00100',
                IbanFormat::KEY_ACCOUNT_NUMBER => '9999901234567890',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'TR32 0010 0099 9990 1234 5678 90',
            ]],
            [++$number, new Iban('VG96VPVG0000012345678901'), true, null, [
                IbanFormat::KEY_COUNTRY_CODE => 'VG',
                IbanFormat::KEY_IBAN_CHECK_DIGITS => '96',
                IbanFormat::KEY_NATIONAL_BANK_CODE => 'VPVG',
                IbanFormat::KEY_ACCOUNT_NUMBER => '0000012345678901',
                IbanFormat::KEY_BALANCE_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_NATIONAL_IDENTIFICATION_NUMBER => null,
                IbanFormat::KEY_CURRENCY_CODE => null,
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => null,
                IbanFormat::KEY_BIC_BANK_CODE => null,
                IbanFormat::KEY_BRANCH_CODE => null,
                IbanFormat::KEY_ACCOUNT_TYPE => null,
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => null,
                IbanFormat::KEY_IBAN_FORMATTED => 'VG96 VPVG 0000 0123 4567 8901',
            ]],
        ];
    }
}
