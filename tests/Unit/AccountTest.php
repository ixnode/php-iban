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
use Ixnode\PhpIban\IbanFormat;
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
     * @param array<string, mixed> $properties
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
        array $properties,
        string|null $expectedIban,
        string|null $lastError = null
    ): void
    {
        /* Arrange */

        /* Act */
        $account = new Account($accountNumber, $nationalBankCode, $countryCode, $properties);
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
            [++$number, '1349870', '60000', 'AT', [], 'AT026000000001349870', ],
            [++$number, '100013997', '09000', 'CH', [], 'CH0209000000100013997', ],
            [++$number, '202051', '12030000', 'DE', [], 'DE02120300000000202051', ],
            [++$number, '17197386', '08800', 'LI', [], 'LI0208800000017197386', ],

            /**
             * Valid tests: DACH + LI Accounts (with leading zeros).
             */
            [++$number, '00001349870', '60000', 'AT', [], 'AT026000000001349870', ],
            [++$number, '000100013997', '09000', 'CH', [], 'CH0209000000100013997', ],
            [++$number, '0000202051', '12030000', 'DE', [], 'DE02120300000000202051', ],
            [++$number, '000017197386', '08800', 'LI', [], 'LI0208800000017197386', ],

            /**
             * Invalid tests (True negative): Account number to long.
             */
            [++$number, '0000001349870', '60000', 'AT', [], null, 'The given value "0000001349870" is too long (c: bbbbbcccccccccccAT00).', ],
            [++$number, '00000100013997', '09000', 'CH', [], null, 'The given value "00000100013997" is too long (c: bbbbbccccccccccccCH00).', ],
            [++$number, '000000202051', '12030000', 'DE', [], null, 'The given value "000000202051" is too long (c: bbbbbbbbccccccccccDE00).', ],
            [++$number, '00000017197386', '08800', 'LI', [], null, 'The given value "00000017197386" is too long (c: bbbbbccccccccccccLI00).', ],

            /**
             * Other valid tests with properties.
             */
            [++$number, '00020053701', '30027', 'FR', [
                IbanFormat::KEY_BRANCH_CODE => '17533',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '59',
            ], 'FR7630027175330002005370159', ],
            [++$number, '0200051332', '2100', 'ES', [
                IbanFormat::KEY_BRANCH_CODE => '0418',
                IbanFormat::KEY_NATIONAL_CHECK_DIGITS => '45',
            ], 'ES9121000418450200051332', ],
            [++$number, '2000145399', '0800', 'CZ', [
                IbanFormat::KEY_ACCOUNT_NUMBER_PREFIX => '000019',
            ], 'CZ6508000000192000145399', ],
            [++$number, '0009795493', '00360305', 'BR', [
                IbanFormat::KEY_BRANCH_CODE => '00001',
                IbanFormat::KEY_ACCOUNT_TYPE => 'P',
                IbanFormat::KEY_OWNER_ACCOUNT_NUMBER => '1',
            ], 'BR9700360305000010009795493P1', ],

        ];
    }
}
