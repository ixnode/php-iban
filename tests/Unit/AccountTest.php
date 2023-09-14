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

use Ixnode\PhpIban\Account;
use Ixnode\PhpIban\Exception\AccountParseException;
use Ixnode\PhpIban\Exception\IbanParseException;
use PHPUnit\Framework\TestCase;

/**
 * Class AccountTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-14)
 * @since 0.1.0 (2023-09-14) First version.
 * @link Account
 */
final class AccountTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProviderAccount
     *
     * @test
     * @testdox $number) Test account number: "$accountNumber"
     * @param int $number
     * @param string $accountNumber
     * @param string $nationalBankCode
     * @param string $countryCode
     * @param string|null $expectedIban
     * @param string|null $lastError
     * @throws AccountParseException
     * @throws IbanParseException
     */
    public function wrapperAccount(
        int $number,
        string $accountNumber,
        string $nationalBankCode,
        string $countryCode,
        string|null $expectedIban,
        string|null $lastError = null
    ): void
    {
        /* Arrange */

        /* Act */
        $account = new Account($accountNumber, $nationalBankCode, $countryCode);
        $iban = $account->getIban();

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertSame(is_null($lastError), $account->isValid());
        $this->assertSame($expectedIban, $iban);

        if (!is_null($lastError)) {
            $this->assertSame($lastError, $account->getLastError());
        }
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, mixed>>
     */
    public function dataProviderAccount(): array
    {
        $number = 0;

        return [

            /**
             * Valid tests: DACH + LI Accounts (without leading zeros).
             */
            [++$number, '1349870', '60000', 'AT', 'AT026000000001349870', ],
            [++$number, '100013997', '09000', 'CH', 'CH0209000000100013997', ],
            [++$number, '202051', '12030000', 'DE', 'DE02120300000000202051', ],
            [++$number, '17197386', '08800', 'LI', 'LI0208800000017197386', ],

            /**
             * Valid tests: DACH + LI Accounts (with leading zeros).
             */
            [++$number, '00001349870', '60000', 'AT', 'AT026000000001349870', ],
            [++$number, '000100013997', '09000', 'CH', 'CH0209000000100013997', ],
            [++$number, '0000202051', '12030000', 'DE', 'DE02120300000000202051', ],
            [++$number, '000017197386', '08800', 'LI', 'LI0208800000017197386', ],

            /**
             * Invalid tests (True negative): Account number to long.
             */
            [++$number, '0000001349870', '60000', 'AT', null, 'The given value "0000001349870" is too long (c: bbbbbcccccccccccAT00).', ],
            [++$number, '00000100013997', '09000', 'CH', null, 'The given value "00000100013997" is too long (c: bbbbbccccccccccccCH00).', ],
            [++$number, '000000202051', '12030000', 'DE', null, 'The given value "000000202051" is too long (c: bbbbbbbbccccccccccDE00).', ],
            [++$number, '00000017197386', '08800', 'LI', null, 'The given value "00000017197386" is too long (c: bbbbbccccccccccccLI00).', ],
        ];
    }
}
