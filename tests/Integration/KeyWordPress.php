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

use Extended\ACF\Key;

$core = getenv('WP_CORE_PATH');
if (!$core || !is_file($core . '/wp-includes/plugin.php')) {
    throw new RuntimeException('Set WP_CORE_PATH to a WordPress core checkout.');
}
// Load real formatting functions and hooks without a database or test stubs.
require $core . '/wp-includes/plugin.php';
define('ABSPATH', $core . '/');
define('WPINC', 'wp-includes');
require $core . '/wp-includes/functions.php';
require $core . '/wp-includes/formatting.php';
require dirname(__DIR__, 2) . '/src/Key.php';
function get_locale(): string
{
    return $GLOBALS['extended_acf_test_locale'] ?? 'en_US';
}

$inputs = [
    'field_name', 'abc123', '123', '_leading_', '___', '', 'GROUP-TEST',
    'hello world', 'className', 'café', 'こんにちは', 'Straße', 'İstanbul',
    "cafe\u{0301}", 'foo--bar', ' foo ', 'a.b', '<b>text</b>', '&nbsp;',
    'a/b', '%41', '%c3%a9', "a\nb", "a\rb", "a\tb", "a\0b", "a\xffb", '0',
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_',
];
foreach ([1, 2, 199, 200, 201, 300] as $length) {
    $inputs[] = substr(str_repeat('Ab_09', $length), 0, $length);
}

$checks = 0;
$same = static function ($expected, $actual) use (&$checks): void {
    if ($expected !== $actual) {
        throw new RuntimeException(var_export([$expected, $actual], true));
    }
    $checks++;
};

add_filter('sanitize_title', 'sanitize_title_with_dashes', 10, 3);
foreach (['en_US', 'de_DE', 'tr_TR'] as $locale) {
    $GLOBALS['extended_acf_test_locale'] = $locale;
    foreach ($inputs as $input) {
        $same(str_replace('-', '_', sanitize_title($input)), Key::sanitize($input));
    }
}

// Eligible identifiers bypass filters; other names still use WordPress.
$custom = static fn($title) => $title . '-custom';
add_filter('sanitize_title', $custom, 20);
$same('field_name', Key::sanitize('FIELD_name'));
$same('field_name_custom', Key::sanitize('Field name'));
$same(str_repeat('a', 200) . '_custom', Key::sanitize(str_repeat('A', 201)));
remove_filter('sanitize_title', $custom, 20);

echo "WordPress sanitizer checks: $checks passed.\n";
