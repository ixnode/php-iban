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
     * @dataProvider dataProvider
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
     * @throws CaseUnsupportedException
     * @throws TypeInvalidException
     * @throws IbanParseException
     */
    public function wrapper(
        int $number,
        Iban $given,
        bool $valid,
        string|null $lastError,
        string|null $countryCode,
        string|null $checksum,
        string|null $accountNumber,
        string|null $bankCode
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
        $this->assertSame($validator->getChecksum(), $checksum);
        $this->assertSame($validator->getAccountNumber(), $accountNumber);
        $this->assertSame($validator->getBankCode(), $bankCode);
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, mixed>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Simple IBAN validator test (positive true tests).
             */
            [++$number, new Iban('DE02120300000000202051'), true, null, 'DE', '02', '0000202051', '12030000'],

            /**
             * Wrong checksum (positive false tests).
             */
            [++$number, new Iban('DE03120300000000202051'), false, 'The checksum does not match.', 'DE', '03', '0000202051', '12030000'],

            /**
             * Wrong length (positive false tests).
             */
            [++$number, new Iban('DE0312030000000020205'), false, 'Invalid length of IBAN given: "DE0312030000000020205" (expected: "DEkkbbbbbbbbcccccccccc").', null, null, null, null],

            /**
             * Wrong country (positive false tests).
             */
            [++$number, new Iban('XX02120300000000202051'), false, 'The given country "XX" is not supported yet.', null, null, null, null],

        ];
    }
}
