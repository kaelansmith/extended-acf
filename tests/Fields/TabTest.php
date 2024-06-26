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

namespace Extended\ACF\Tests\Fields;

use Extended\ACF\Fields\Tab;
use Extended\ACF\Tests\Fields\Settings\ConditionalLogic;
use Extended\ACF\Tests\Fields\Settings\Endpoint;
use InvalidArgumentException;

class TabTest extends FieldTestCase
{
    use ConditionalLogic;
    use Endpoint;

    public string $field = Tab::class;
    public string $type = 'tab';

    public function testPlacement()
    {
        $field = Tab::make('Placement')->placement('top')->get();
        $this->assertSame('top', $field['placement']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument placement [test].');

        Tab::make('Invalid Placement')->placement('test')->get();
    }

    public function testSelected()
    {
        $field = Tab::make('Selected')->selected()->get();
        $this->assertTrue($field['selected']);
    }
}
