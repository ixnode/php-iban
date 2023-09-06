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

namespace Ixnode\PhpIban\Command\Base;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use Exception;
use Ixnode\PhpIban\Validator;

/**
 * Abstract class BaseCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 * @property string|null $iban
 */
abstract class BaseCommand extends Command
{
    protected const SUCCESS = 0;

    protected const INVALID = 2;

    /**
     * Prints error message.
     *
     * @param string $message
     * @return void
     * @throws Exception
     */
    protected function printError(string $message): void
    {
        $color = new Color();

        $this->writer()->write(sprintf('%s%s', $color->error($message), PHP_EOL));
    }

    /**
     * Prints a given message.
     *
     * @param string $message
     * @return void
     * @throws Exception
     */
    protected function writeln(string $message): void
    {
        $this->writer()->write($message.PHP_EOL);
    }

    /**
     * Prints the given validator.
     *
     * @param Validator $validator
     * @return void
     * @throws Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function printIban(Validator $validator): void
    {
        $iban = $validator->getIban();
        $account = $validator->getAccount();

        $this->writeln('');
        $this->writeln('Parsed IBAN');
        $this->writeln('-----------');
        $this->writeln(sprintf('Valid:                           %s', $validator->isValid() ? 'YES' : 'NO'));
        $this->writeln(sprintf('Last error:                      %s', $validator->hasLastError() ? $validator->getLastError() : 'N/A'));
        $this->writeln(sprintf('IBAN:                            %s', $iban->getIban()));
        $this->writeln(sprintf('IBAN:                            %s', $iban->getIbanFormatted()));
        $this->writeln(sprintf('Checksum:                        %s', $iban->getIbanCheckDigits()));
        $this->writeln(sprintf('Format:                          %s', $iban->getIbanFormat()?->getIbanFormat() ?: 'N/A'));
        $this->writeln(sprintf('Parts:                           %s', http_build_query($iban->getParts(),'',', ')));

        $this->writeln('');
        $this->writeln('Account');
        $this->writeln('-------');
        $this->writeln(sprintf('Country:                         %s (%s)', $account?->getCountryCode() ?: 'N/A', $account?->getCountryName() ?: 'N/A'));
        $this->writeln(sprintf('Checksum:                        %s', $account?->getIbanCheckDigits() ?: 'N/A'));

        $this->writeln(sprintf('Balance cccount number:          %s', $account?->getBalanceAccountNumber() ?: 'N/A'));
        $this->writeln(sprintf('National bank code:              %s', $account?->getNationalBankCode() ?: 'N/A'));
        $this->writeln(sprintf('Account number:                  %s', $account?->getAccountNumber() ?: 'N/A'));
        $this->writeln(sprintf('National identification number:  %s', $account?->getNationalIdentificationNumber() ?: 'N/A'));
        $this->writeln(sprintf('Currency code:                   %s', $account?->getCurrencyCode() ?: 'N/A'));
        $this->writeln(sprintf('Owner account number:            %s', $account?->getOwnerAccountNumber() ?: 'N/A'));
        $this->writeln(sprintf('Account number prefix:           %s', $account?->getAccountNumberPrefix() ?: 'N/A'));
        $this->writeln(sprintf('Bic bank code:                   %s', $account?->getBicBankCode() ?: 'N/A'));
        $this->writeln(sprintf('Branch code:                     %s', $account?->getBranchCode() ?: 'N/A'));
        $this->writeln(sprintf('Account type:                    %s', $account?->getAccountType() ?: 'N/A'));
        $this->writeln(sprintf('National check digits:           %s', $account?->getNationalCheckDigits() ?: 'N/A'));

        $this->writeln(sprintf('IBAN (from account):             %s', $account?->getIban()));
        $this->writeln(sprintf('IBAN (from account):             %s', $account?->getIbanFormatted()));
        $this->writeln('');
    }
}
