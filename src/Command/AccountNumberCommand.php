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

namespace Ixnode\PhpIban\Command;

use Exception;
use Ixnode\PhpIban\AccountNumber;
use Ixnode\PhpIban\Command\Base\BaseCommand;
use Ixnode\PhpIban\Validator;

/**
 * Class AccountNumberCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 * @property string|null $accountNumber
 * @property string|null $bankCode
 * @property string|null $countryCode
 */
class AccountNumberCommand extends BaseCommand
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('account-number:validate', 'Validates the given account number, bank and country code.');

        $this
            ->argument('account-number', 'The account number.')
            ->argument('bank-code', 'The bank code.')
            ->argument('country-code', 'The country code.', 'DE');
        ;
    }

    /**
     * Executes the ParserCommand.
     *
     * @return int
     * @throws Exception
     */
    public function execute(): int
    {
        $accountNumber = $this->accountNumber;
        $bankCode = $this->bankCode;
        $countryCode = $this->countryCode;

        if (is_null($accountNumber)) {
            $this->printError('No account number given.');
            return self::INVALID;
        }

        if (is_null($bankCode)) {
            $this->printError('No bank code given.');
            return self::INVALID;
        }

        if (is_null($countryCode)) {
            $this->printError('No country code given.');
            return self::INVALID;
        }

        $validator = new Validator(new AccountNumber($accountNumber, $bankCode, $countryCode));

        $this->writeln('');
        $this->writeln(sprintf('Given account number: %s', $accountNumber));
        $this->writeln(sprintf('Given bank code:      %s', $bankCode));
        $this->writeln(sprintf('Given country code:   %s', $countryCode));
        $this->printIban($validator);
        $this->writeln('');

        return self::SUCCESS;
    }
}
