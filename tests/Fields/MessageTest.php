<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/wordplate/extended-acf
 */

declare(strict_types=1);

namespace WordPlate\Tests\Acf\Fields;

use PHPUnit\Framework\TestCase;
use WordPlate\Acf\Fields\Message;

class MessageTest extends TestCase
{
    public function testType()
    {
        $field = Message::make('Message')->get();
        $this->assertSame('message', $field['type']);
    }

    public function testEscapeHtml()
    {
        $field = Message::make('Message Escape HTML')->escapeHtml()->get();
        $this->assertTrue($field['esc_html']);
    }

    public function testMessage()
    {
        $field = Message::make('Message Content')->message('The Content')->get();
        $this->assertSame('The Content', $field['message']);
    }
}
