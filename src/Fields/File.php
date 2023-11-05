<?php

/**
 * Copyright (c) Vincent Klaiber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/extended-acf
 */

declare(strict_types=1);

namespace Extended\ACF\Fields;

use Extended\ACF\Fields\Settings\ConditionalLogic;
use Extended\ACF\Fields\Settings\FileSize;
use Extended\ACF\Fields\Settings\FileTypes;
use Extended\ACF\Fields\Settings\HelperText;
use Extended\ACF\Fields\Settings\Library;
use Extended\ACF\Fields\Settings\Required;
use Extended\ACF\Fields\Settings\Wrapper;
use InvalidArgumentException;

class File extends Field
{
    use ConditionalLogic;
    use FileSize;
    use FileTypes;
    use HelperText;
    use Library;
    use Required;
    use Wrapper;

    protected string|null $type = 'file';

    /**
     * @param string $format array, id, url
     * @throws \InvalidArgumentException
     */
    public function format(string $format): static
    {
        if (!in_array($format, ['array', 'id', 'url'])) {
            throw new InvalidArgumentException("Invalid argument format [$format].");
        }

        $this->settings['return_format'] = $format;

        return $this;
    }
}
