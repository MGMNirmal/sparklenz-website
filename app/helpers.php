<?php

use App\Models\PageCopy;
use App\Models\Setting;
use App\Support\Money;
use Illuminate\Support\HtmlString;

if (! function_exists('page_copy')) {
    /**
     * Editable page copy as plain text, keyed "page.key".
     */
    function page_copy(string $key, string $default = ''): string
    {
        $value = PageCopy::map()[$key] ?? null;

        return filled($value) ? (string) $value : $default;
    }
}

if (! function_exists('page_copy_html')) {
    /**
     * Editable page copy escaped for HTML, with line breaks preserved.
     */
    function page_copy_html(string $key, string $default = ''): HtmlString
    {
        return new HtmlString(nl2br(e(page_copy($key, $default)), false));
    }
}

if (! function_exists('page_copy_lines')) {
    /**
     * Editable page copy split into non-empty lines.
     *
     * @return list<string>
     */
    function page_copy_lines(string $key): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', page_copy($key)) ?: [])));
    }
}

if (! function_exists('site_setting')) {
    function site_setting(string $key, ?string $default = null): ?string
    {
        $value = Setting::map()[$key] ?? null;

        return filled($value) ? (string) $value : $default;
    }
}

if (! function_exists('lkr')) {
    function lkr(?int $amount): string
    {
        return Money::lkr($amount);
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(?string $message = null): ?string
    {
        $number = preg_replace('/\D/', '', (string) site_setting('whatsapp_number'));

        if ($number === '') {
            return null;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message ?? (string) site_setting('whatsapp_message', ''));
    }
}
