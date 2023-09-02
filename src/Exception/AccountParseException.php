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

namespace Ixnode\PhpIban\Exception;

use Ixnode\PhpException\Base\BaseException;

/**
 * Class AccountParseException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final class AccountParseException extends BaseException
{
    public const TEXT_PLACEHOLDER = '%s';

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $message);

        parent::__construct($messageNonVerbose);
    }
}
