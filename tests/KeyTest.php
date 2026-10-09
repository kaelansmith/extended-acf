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

namespace Extended\Tests\ACF;

use Extended\ACF\Key;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class KeyTest extends TestCase
{
    public function testGenerate()
    {
        $key = Key::generate('block_image', 'layout');

        $this->assertSame('layout_2f1c419c', $key);
    }

    public function testValidateUnique()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The key [video] is not unique.');

        Key::generate('video', 'layout');
        Key::generate('video', 'layout');
    }

    public function testSetHas()
    {
        Key::set('field_123', 'field_123');
        $this->assertTrue(Key::has('field_123'));
    }

    public function testSanitize()
    {
        $this->assertSame('group_test', Key::sanitize('GROUP-TEST'));
    }

    public function testSanitizeAsciiIdentifiers()
    {
        $this->assertSame('field_name_123', Key::sanitize('FIELD_Name_123'));
        $this->assertSame('_leading_', Key::sanitize('_LEADING_'));
        $this->assertSame('0', Key::sanitize('0'));
        $this->assertSame(str_repeat('a', 200), Key::sanitize(str_repeat('A', 200)));
    }

    public function testHash()
    {
        $this->assertSame('b35135fa', Key::hash('image'));
    }
}
