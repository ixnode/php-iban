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
use Ixnode\PhpIban\Command\Base\BaseCommand;
use Ixnode\PhpIban\Iban;
use Ixnode\PhpIban\Validator;

/**
 * Class IbanCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 * @property string|null $iban
 */
class IbanCommand extends BaseCommand
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('iban:validate', 'Validates the given IBAN number.');

        $this
            ->argument('iban', 'The IBAN number which should be validated.')
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
        $iban = $this->iban;

        if (is_null($iban)) {
            $this->printError('No iban given.');
            return self::INVALID;
        }

        $validator = new Validator(new Iban($iban));

        $this->writeln('');
        $this->writeln(sprintf('Given IBAN:     %s', $iban));
        $this->printIban($validator);
        $this->writeln('');

        return self::SUCCESS;
    }
}
