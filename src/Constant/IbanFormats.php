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

namespace Ixnode\PhpIban\Constant;

/**
 * Class IbanFormats
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2023-09-01)
 * @since 0.1.0 (2023-09-01) First version.
 */
final class IbanFormats
{
    final public const IBAN_FORMATS = [
        'AT' => 'ATkk bbbb bccc cccc cccc',
        'CH' => 'CHkk bbbb bccc cccc cccc c',
        'DE' => 'DEkk bbbb bbbb cccc cccc cc',
    ];
}
