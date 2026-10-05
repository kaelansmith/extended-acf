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

namespace Extended\ACF;

use InvalidArgumentException;

class Key
{
    protected static array $keys = [];

    /** @throws \InvalidArgumentException */
    public static function generate(string $key, string $prefix): string
    {
        if (static::has($key)) {
            throw new InvalidArgumentException("The key [$key] is not unique.");
        }

        $hashedKey = $prefix . '_' . static::hash($key);

        static::set($key, $hashedKey);

        return $hashedKey;
    }

    public static function set(string $key, string $value): void
    {
        static::$keys[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$keys);
    }

    public static function hash(string $key): string
    {
        return hash('fnv1a32', $key);
    }

    public static function sanitize(string $key): string
    {
        // For 1-200 ASCII letters, digits and underscores, WordPress's default
        // sanitizer produces the lowercase input. Other inputs and hook
        // configurations use the full pipeline. The shortcut skips filter
        // dispatch and its call counter.
        $hook = $GLOBALS['wp_filter']['sanitize_title'] ?? null;
        $all = $GLOBALS['wp_filter']['all'] ?? null;
        if (
            strlen($key) <= 200 && preg_match('/\A[a-zA-Z0-9_]+\z/D', $key)
            && $hook instanceof \WP_Hook
            && count($hook->callbacks) === 1
            && count($hook->callbacks[10] ?? []) === 1
            && ($hook->callbacks[10]['sanitize_title_with_dashes']['function'] ?? null) === 'sanitize_title_with_dashes'
            && ($hook->callbacks[10]['sanitize_title_with_dashes']['accepted_args'] ?? null) === 3
            && empty($all->callbacks)
        ) {
            return strtolower($key);
        }

        return str_replace('-', '_', sanitize_title($key));
    }

    public static function resolveParentKey(?string $parentKey, string $key): string
    {
        $parentKeyPieces = explode('_', $parentKey);

        while (count($parentKeyPieces) > 1) {
            $potentialParentKey = implode('_', $parentKeyPieces);
            $potentialKey = $potentialParentKey . '_' . $key;

            if (array_key_exists($potentialKey, self::$keys)) {
                return $potentialParentKey;
            }

            array_pop($parentKeyPieces);
        }

        return $parentKey;
    }
}
