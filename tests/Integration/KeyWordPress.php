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
$same = static function ($expected, $actual, string $message) use (&$checks): void {
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': ' . var_export([$expected, $actual], true));
    }
    $checks++;
};
$capture = static function (callable $sanitize, string $input): array {
    try {
        return ['value', $sanitize($input)];
    } catch (Throwable $error) {
        return ['error', $error::class, $error->getMessage()];
    }
};
$compare = static function () use ($inputs, $same, $capture): void {
    foreach ($inputs as $input) {
        $same(
            $capture(static fn($value) => str_replace('-', '_', sanitize_title($value)), $input),
            $capture([Key::class, 'sanitize'], $input),
            'Sanitizer parity failed for ' . var_export($input, true),
        );
    }
};
$dispatched = static function () use ($same): void {
    $before = did_filter('sanitize_title');
    Key::sanitize('FIELD_name');
    $same($before + 1, did_filter('sanitize_title'), 'A modified hook was bypassed');
};

add_filter('sanitize_title', 'sanitize_title_with_dashes', 10, 3);
$compare();
$before = did_filter('sanitize_title');
$same('field_name', Key::sanitize('FIELD_name'), 'ASCII result changed');
$same($before, did_filter('sanitize_title'), 'The default fast path dispatched the hook');

$customCalls = 0;
$custom = static function ($title, $rawTitle, $context) use (&$customCalls, $same) {
    $customCalls++;
    $same('save', $context, 'Custom filter context changed');
    return $title . '-custom';
};
foreach ([5, 10, 20] as $priority) {
    $customCalls = 0;
    add_filter('sanitize_title', $custom, $priority, 3);
    $compare();
    $same(count($inputs) * 2, $customCalls, 'A custom filter was bypassed');
    $dispatched();
    remove_filter('sanitize_title', $custom, $priority);
    $compare();
}

$observed = 0;
$observer = static function ($hook) use (&$observed): void {
    if ($hook === 'sanitize_title') {
        $observed++;
    }
};
add_filter('all', $observer);
$compare();
$same(count($inputs) * 2, $observed, 'The all hook was bypassed');
remove_filter('all', $observer);
$compare();

remove_filter('sanitize_title', 'sanitize_title_with_dashes', 10);
$compare();
$dispatched();
remove_all_filters('sanitize_title');
$compare();
$dispatched();

// A moved native callback or changed argument count must retain dispatch,
// including WordPress's existing exception for a zero-argument registration.
add_filter('sanitize_title', 'sanitize_title_with_dashes', 11, 3);
$compare();
$dispatched();
remove_filter('sanitize_title', 'sanitize_title_with_dashes', 11);
foreach ([0, 1, 2] as $acceptedArgs) {
    add_filter('sanitize_title', 'sanitize_title_with_dashes', 10, $acceptedArgs);
    $compare();
    if ($acceptedArgs !== 0) {
        $dispatched();
    }
    remove_filter('sanitize_title', 'sanitize_title_with_dashes', 10);
}

add_filter('sanitize_title', $custom, 10, 3);
$compare();
$dispatched();
remove_filter('sanitize_title', $custom, 10);
add_filter('sanitize_title', 'sanitize_title_with_dashes', 10, 3);
foreach (['en_US', 'de_DE', 'tr_TR'] as $locale) {
    $GLOBALS['extended_acf_test_locale'] = $locale;
    $compare();
}

echo "WordPress sanitizer parity: $checks checks passed.\n";
