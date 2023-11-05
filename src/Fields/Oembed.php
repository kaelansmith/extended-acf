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
use Extended\ACF\Fields\Settings\Height;
use Extended\ACF\Fields\Settings\HelperText;
use Extended\ACF\Fields\Settings\Required;
use Extended\ACF\Fields\Settings\Wrapper;

class Oembed extends Field
{
    use ConditionalLogic;
    use Height;
    use HelperText;
    use Required;
    use Wrapper;

    protected string|null $type = 'oembed';

    public function width(int $width): static
    {
        $this->settings['width'] = $width;

        return $this;
    }
}
