<?php

use spark\drivers\I18n\Locale;

/**
 * Translates a string
 *
 * @param string $msgid String to be translated
 * @param string $textdomain Textdomain, if left empty default will be used
 *
 * @return string translated string (or original, if not found)
 */
function __($msgid, $textdomain = null, array $variables = [])
{
    return app()->locale->gettext($msgid, $textdomain, $variables);
}

/**
 * Translates a string with escaping
 *
 * @param string $msgid String to be translated
 * @param string $textdomain Textdomain, if left empty default will be used
 *
 * @return string translated string (or original, if not found)
 */
function _e($msgid, $textdomain = null, array $variables = [])
{
    return html_escape(__($msgid, $textdomain, $variables));
}


/**
 * Register a textdomain
 *
 * @param  string $localeFile
 * @param  string $textdomain
 * @return boolean
 */
function load_textdomain($localeFile, $textdomain)
{
    return app()->locale->register($localeFile, $textdomain);
}

/**
 * Load theme text domain
 *
 * @param  string $locale
 * @param  string $textdomain
 * @return boolean
 */
function load_theme_locale($locale)
{
    $themeLocalePath = theme_locale_path($locale);
    load_textdomain($themeLocalePath, _T);
    registry_store('_spark.theme.locale', $locale, true);
    return true;
}

/**
 * Get cookie locale
 *
 * @return string
 */
function get_cookie_locale()
{
    return get_cookie(Locale::COOKIE_NAME);
}

/**
 * Get site locale
 *
 * @return string
 */
function get_site_locale()
{
    return get_option('site_locale');
}

/**
 * Localizes numbers by using a textdomain
 *
 * @param  string $string
 * @param  mixed $textdomain
 * @return string
 */
function localize_numbers($string, $textdomain = null)
{
    $replacements = [];

    for ($i=0; $i < 10; $i++) {
        $replacements["{$i}"] = __("num_{$i}", $textdomain, ['defaultValue' => $i]);
    }

    return str_ireplace(array_keys($replacements), array_values($replacements), $string);
}
