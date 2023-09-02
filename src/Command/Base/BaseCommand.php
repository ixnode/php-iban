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
     */
    protected function printIban(Validator $validator): void
    {
        $this->writeln('');
        $this->writeln('Parsed IBAN');
        $this->writeln('-----------');
        $this->writeln(sprintf('IBAN:              %s', $validator->getIban()));
        $this->writeln(sprintf('IBAN (formatted):  %s', $validator->getIbanFormatted()));
        $this->writeln(sprintf('Valid:             %s', $validator->isValid() ? 'YES' : 'NO'));
        $this->writeln(sprintf('Last error:        %s', $validator->hasLastError() ? $validator->getLastError() : 'N/A'));
        $this->writeln(sprintf('Country:           %s', $validator->getCountryCode()));
        $this->writeln(sprintf('Checksum:          %s', $validator->getIbanCheckDigits()));
        $this->writeln(sprintf('Account number:    %s', $validator->getAccountNumber()));
        $this->writeln(sprintf('Bank number:       %s', $validator->getNationalBankCode()));
        $this->writeln('');
    }
}
