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
     * @param string|null $countryCode
     * @param string|null $checksum
     * @param string|null $accountNumber
     * @param string|null $bankCode
     * @param string|null $ibanFormatted
     * @throws CaseUnsupportedException
     * @throws IbanParseException
     * @throws TypeInvalidException
     */
    public function wrapperIban(
        int $number,
        Iban $given,
        bool $valid,
        string|null $lastError,
        string|null $countryCode,
        string|null $checksum,
        string|null $accountNumber,
        string|null $bankCode,
        string|null $ibanFormatted,
    ): void
    {
        /* Arrange */

        /* Act */
        $validator = new Validator($given);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertSame($validator->isValid(), $valid);
        $this->assertSame($validator->getLastError(), $lastError);
        $this->assertSame($validator->getCountryCode(), $countryCode);
        $this->assertSame($validator->getIbanCheckDigits(), $checksum);
        $this->assertSame($validator->getAccountNumber(), $accountNumber);
        $this->assertSame($validator->getNationalBankCode(), $bankCode);
        $this->assertSame($validator->getIbanFormatted(), $ibanFormatted);
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, mixed>>
     */
    public function dataProviderIban(): array
    {
        $number = 0;

        return [

            /**
             * Simple IBAN validator test (positive true tests).
             *
             * @see [AT,CH,DE,LI] https://ibanvalidieren.de/beispiele.html
             */
            [++$number, new Iban('AT026000000001349870'), true, null, 'AT', '02', '00001349870', '60000', 'AT02 6000 0000 0134 9870'],
            [++$number, new Iban('CH0209000000100013997'), true, null, 'CH', '02', '000100013997', '09000', 'CH02 0900 0000 1000 1399 7'],
            [++$number, new Iban('DE02120300000000202051'), true, null, 'DE', '02', '0000202051', '12030000', 'DE02 1203 0000 0000 2020 51'],
            [++$number, new Iban('LI0208800000017197386'), true, null, 'LI', '02', '000017197386', '08800', 'LI02 0880 0000 0171 9738 6'],

            /**
             * Wrong checksum (positive false tests).
             */
            [++$number, new Iban('DE03120300000000202051'), false, 'The checksum does not match.', 'DE', '03', '0000202051', '12030000', 'DE03 1203 0000 0000 2020 51'],

            /**
             * Wrong length (positive false tests).
             */
            [++$number, new Iban('DE0312030000000020205'), false, 'Invalid length of IBAN given: "DE0312030000000020205" (expected: "DEkkbbbbbbbbcccccccccc").', null, null, null, null, 'DE03 1203 0000 0000 2020 5'],

            /**
             * Wrong country (positive false tests).
             */
            [++$number, new Iban('XX02120300000000202051'), false, 'The given country "XX" is not supported yet.', null, null, null, null, 'XX02 1203 0000 0000 2020 51'],

        ];
    }
}
