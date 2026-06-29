<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('branding')) {
    /**
     * Get a branding value, merging config defaults with stored overrides.
     *
     * @param  string  $key  Dot-notation key, e.g. "logo.text" or "logo.gradient_from"
     */
    function branding(string $key, mixed $default = null): mixed
    {
        $overrides = [];

        if (Storage::exists('branding.json')) {
            $overrides = json_decode(Storage::get('branding.json'), true) ?? [];
        }

        $value = data_get($overrides, $key) ?? config("branding.{$key}", $default);

        return $value;
    }
}

if (! function_exists('branding_css')) {
    /**
     * Generate CSS custom properties for branding colors.
     */
    function branding_css(): string
    {
        $from = branding('logo.gradient_from_hex', '#ef4444');
        $to = branding('logo.gradient_to_hex', '#f97316');

        return "--brand-from: {$from}; --brand-to: {$to};";
    }
}

if (! function_exists('set_env')) {
    /**
     * Set a value in the .env file.
     *
     * @param  string  $key  The environment variable name.
     * @param  string  $value  The value to set.
     */
    function set_env(string $key, string $value): void
    {
        $path = app()->environmentFilePath();

        if (! file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        // Escape value if it contains spaces or special chars
        if (str_contains($value, ' ') || str_contains($value, '#') || str_contains($value, '"') || str_contains($value, "'")) {
            $value = '"'.addcslashes($value, '\\"').'"';
        }

        $key = strtoupper($key);

        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing key
            $content = preg_replace(
                "/^{$key}=.*$/m",
                "{$key}={$value}",
                $content,
            );
        } else {
            // Append new key at the end
            $content = rtrim($content, "\n")."\n{$key}={$value}\n";
        }

        file_put_contents($path, $content);

        // Reload environment for current request
        if (function_exists('apache_setenv')) {
            apache_setenv($key, $value);
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }
}
