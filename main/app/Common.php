<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('safe_external_url')) {
    /**
     * Allow-list external URLs to http(s). Anything else (javascript:,
     * data:, vbscript:, ...) becomes '#' so stored URLs can't execute.
     */
    function safe_external_url(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '#';
        }
        // Protocol-relative URLs inherit the page scheme (http here).
        if (str_starts_with($url, '//')) {
            return 'http:' . $url;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if ($scheme === '' || $scheme === 'http' || $scheme === 'https') {
            // No scheme = relative path; http(s) = fine.
            return $url;
        }
        return '#';
    }
}
