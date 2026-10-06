# WordPress sanitizer integration checks

The ordinary PHPUnit suite uses formatting stubs. This standalone check exercises
`Key::sanitize()` against real WordPress formatting functions and `WP_Hook`.
It requires a WordPress core checkout, but no database, plugins, or installed site.
Run it separately from the stub-based suite:

```sh
WP_CORE_PATH=/absolute/path/to/wordpress php tests/Integration/KeyWordPress.php
```

The check covers ASCII identifiers, mixed case, punctuation, empty input, Unicode,
invalid UTF-8, the 200-byte boundary, multiple locales, custom sanitizer callbacks,
callback removal/replacement, changed priorities and argument counts, and `all`
observers. It compares values and exceptions with the original WordPress pipeline.

The shortcut intentionally skips `sanitize_title` dispatch for eligible inputs
under the default hook configuration. Consequently, those calls do not increment
`did_filter('sanitize_title')`. The check documents this observable difference and
asserts that modified hook configurations retain dispatch.
