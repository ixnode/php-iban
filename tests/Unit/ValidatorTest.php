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
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;
use Ixnode\PhpIban\Iban;
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
    private const KEY_COUNTRY_CODE = 'country-code';

    private const KEY_IBAN_CHECK_DIGITS = 'iban-check-digits';

    private const KEY_NATIONAL_BANK_CODE = 'national-bank-code';

    private const KEY_ACCOUNT_NUMBER = 'account-number';

    private const KEY_BRANCH_CODE = 'branch-code';

    private const KEY_NATIONAL_CHECK_DIGITS = 'national-check-digits';

    private const KEY_IBAN_FORMATTED = 'iban-formatted';



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
            self::KEY_COUNTRY_CODE,
            self::KEY_IBAN_CHECK_DIGITS,
            self::KEY_NATIONAL_BANK_CODE,
            self::KEY_ACCOUNT_NUMBER,
            self::KEY_BRANCH_CODE,
            self::KEY_NATIONAL_CHECK_DIGITS,
            self::KEY_IBAN_FORMATTED,
        ], array_keys($expected));

        $this->assertSame($expected, [
            self::KEY_COUNTRY_CODE => $validator->getCountryCode(),
            self::KEY_IBAN_CHECK_DIGITS => $validator->getIbanCheckDigits(),
            self::KEY_NATIONAL_BANK_CODE => $validator->getNationalBankCode(),
            self::KEY_ACCOUNT_NUMBER => $validator->getAccount(),
            self::KEY_BRANCH_CODE => $validator->getBranchCode(),
            self::KEY_NATIONAL_CHECK_DIGITS => $validator->getNationalCheckDigits(),
            self::KEY_IBAN_FORMATTED => $validator->getIbanFormatted(),
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
                self::KEY_COUNTRY_CODE => 'AT',
                self::KEY_IBAN_CHECK_DIGITS => '02',
                self::KEY_NATIONAL_BANK_CODE => '60000',
                self::KEY_ACCOUNT_NUMBER => '00001349870',
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'AT02 6000 0000 0134 9870',
            ]],
            [++$number, new Iban('CH0209000000100013997'), true, null, [
                self::KEY_COUNTRY_CODE => 'CH',
                self::KEY_IBAN_CHECK_DIGITS => '02',
                self::KEY_NATIONAL_BANK_CODE => '09000',
                self::KEY_ACCOUNT_NUMBER => '000100013997',
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'CH02 0900 0000 1000 1399 7',
            ]],
            [++$number, new Iban('DE02120300000000202051'), true, null, [
                self::KEY_COUNTRY_CODE => 'DE',
                self::KEY_IBAN_CHECK_DIGITS => '02',
                self::KEY_NATIONAL_BANK_CODE => '12030000',
                self::KEY_ACCOUNT_NUMBER => '0000202051',
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('LI0208800000017197386'), true, null, [
                self::KEY_COUNTRY_CODE => 'LI',
                self::KEY_IBAN_CHECK_DIGITS => '02',
                self::KEY_NATIONAL_BANK_CODE => '08800',
                self::KEY_ACCOUNT_NUMBER => '000017197386',
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'LI02 0880 0000 0171 9738 6',
            ]],

            /**
             * FR: Simple IBAN validator test (positive true tests).
             *
             * @see https://www.iban.de/iban-laenderliste.html
             */
            [++$number, new Iban('FR1420041010050500013M02606'), true, null, [
                self::KEY_COUNTRY_CODE => 'FR',
                self::KEY_IBAN_CHECK_DIGITS => '14',
                self::KEY_NATIONAL_BANK_CODE => '20041',
                self::KEY_ACCOUNT_NUMBER => '0500013M026',
                self::KEY_BRANCH_CODE => '01005',
                self::KEY_NATIONAL_CHECK_DIGITS => '06',
                self::KEY_IBAN_FORMATTED => 'FR14 2004 1010 0505 0001 3M02 606',
            ]],
            [++$number, new Iban('FR7630027175330002005370159'), true, null, [
                self::KEY_COUNTRY_CODE => 'FR',
                self::KEY_IBAN_CHECK_DIGITS => '76',
                self::KEY_NATIONAL_BANK_CODE => '30027',
                self::KEY_ACCOUNT_NUMBER => '00020053701',
                self::KEY_BRANCH_CODE => '17533',
                self::KEY_NATIONAL_CHECK_DIGITS => '59',
                self::KEY_IBAN_FORMATTED => 'FR76 3002 7175 3300 0200 5370 159',
            ]],
            [++$number, new Iban('FR7630006000011234567890189'), true, null, [
                self::KEY_COUNTRY_CODE => 'FR',
                self::KEY_IBAN_CHECK_DIGITS => '76',
                self::KEY_NATIONAL_BANK_CODE => '30006',
                self::KEY_ACCOUNT_NUMBER => '12345678901',
                self::KEY_BRANCH_CODE => '00001',
                self::KEY_NATIONAL_CHECK_DIGITS => '89',
                self::KEY_IBAN_FORMATTED => 'FR76 3000 6000 0112 3456 7890 189',
            ]],

            /**
             * Wrong checksum (positive false tests).
             */
            [++$number, new Iban('DE03120300000000202051'), false, 'The checksum does not match.', [
                self::KEY_COUNTRY_CODE => 'DE',
                self::KEY_IBAN_CHECK_DIGITS => '03',
                self::KEY_NATIONAL_BANK_CODE => '12030000',
                self::KEY_ACCOUNT_NUMBER => '0000202051',
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'DE03 1203 0000 0000 2020 51',
            ]],
            [++$number, new Iban('FR7530006000011234567890189'), false, 'The checksum does not match.', [
                self::KEY_COUNTRY_CODE => 'FR',
                self::KEY_IBAN_CHECK_DIGITS => '75',
                self::KEY_NATIONAL_BANK_CODE => '30006',
                self::KEY_ACCOUNT_NUMBER => '12345678901',
                self::KEY_BRANCH_CODE => '00001',
                self::KEY_NATIONAL_CHECK_DIGITS => '89',
                self::KEY_IBAN_FORMATTED => 'FR75 3000 6000 0112 3456 7890 189',
            ]],

            /**
             * Wrong length (positive false tests).
             */
            [++$number, new Iban('DE0212030000000020205'), false, 'Invalid length of IBAN given: "DE0212030000000020205" (expected: "DEkkbbbbbbbbcccccccccc").', [
                self::KEY_COUNTRY_CODE => null,
                self::KEY_IBAN_CHECK_DIGITS => null,
                self::KEY_NATIONAL_BANK_CODE => null,
                self::KEY_ACCOUNT_NUMBER => null,
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 5',
            ]],
            [++$number, new Iban('DE021203000000002020512'), false, 'Invalid length of IBAN given: "DE021203000000002020512" (expected: "DEkkbbbbbbbbcccccccccc").', [
                self::KEY_COUNTRY_CODE => null,
                self::KEY_IBAN_CHECK_DIGITS => null,
                self::KEY_NATIONAL_BANK_CODE => null,
                self::KEY_ACCOUNT_NUMBER => null,
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'DE02 1203 0000 0000 2020 512',
            ]],

            /**
             * Wrong country (positive false tests).
             */
            [++$number, new Iban('XX02120300000000202051'), false, 'The given country "XX" is not supported yet.', [
                self::KEY_COUNTRY_CODE => null,
                self::KEY_IBAN_CHECK_DIGITS => null,
                self::KEY_NATIONAL_BANK_CODE => null,
                self::KEY_ACCOUNT_NUMBER => null,
                self::KEY_BRANCH_CODE => null,
                self::KEY_NATIONAL_CHECK_DIGITS => null,
                self::KEY_IBAN_FORMATTED => 'XX02 1203 0000 0000 2020 51',
            ]],
        ];
    }
}
