<?php

use FleetCart\FleetCart;
use Modules\Support\RTLDetector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\Intl\Currencies;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (!function_exists('str_between')) {
    /**
     * Get the portion of a string between the given values.
     *
     * @param string $subject
     * @param string $search
     *
     * @return string
     */
    function str_between($subject, $startsWith, $endsWith)
    {
        return str_after(str_before($subject, $endsWith), $startsWith);
    }
}

if (!function_exists('locale')) {
    /**
     * Get current locale.
     *
     * @return string
     */
    function locale()
    {
        return app()->getLocale();
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Determine if the given / current locale is RTL script.
     *
     * @param string|null $locale
     *
     * @return bool
     */
    function is_rtl($locale = null)
    {
        return RTLDetector::detect($locale ?: locale());
    }
}

if (!function_exists('currency')) {
    /**
     * Get current currency.
     *
     * @return string
     */
    function currency()
    {
        if (app('inAdminPanel')) {
            return setting('default_currency');
        }

        $currency = Cookie::get('currency');

        if (!in_array($currency, setting('supported_currencies'))) {
            $currency = setting('default_currency');
        }

        return $currency;
    }
}

if (!function_exists('supported_locales')) {
    /**
     * Get all supported locales.
     *
     * @return array
     */
    function supported_locales()
    {
        return LaravelLocalization::getSupportedLocales();
    }
}

if (!function_exists('supported_locale_keys')) {
    /**
     * Get all supported locale keys.
     *
     * @return array
     */
    function supported_locale_keys()
    {
        return LaravelLocalization::getSupportedLanguagesKeys();
    }
}

if (!function_exists('localized_url')) {
    /**
     * Returns a URL adapted to the given locale.
     *
     * @param string $locale
     * @param string $url
     *
     * @return string
     */
    function localized_url($locale, $url = null)
    {
        return LaravelLocalization::getLocalizedURL($locale, $url);
    }
}

if (!function_exists('non_localized_url')) {
    /**
     * It returns a URL without locale.
     *
     * @param string $url
     *
     * @return string
     */
    function non_localized_url($url = null)
    {
        return LaravelLocalization::getNonLocalizedURL($url);
    }
}

if (!function_exists('is_multilingual')) {
    /**
     * Determine if the app has multi-language.
     *
     * @return bool
     */
    function is_multilingual()
    {
        return count(supported_locales()) > 1;
    }
}

if (!function_exists('is_multi_currency')) {
    /**
     * Determine if the app has multi currency.
     *
     * @return bool
     */
    function is_multi_currency()
    {
        return count(setting('supported_currencies')) > 1;
    }
}

if (!function_exists('is_module_enabled')) {
    /**
     * Determine if the given module is enabled.
     *
     * @param string $module
     *
     * @return bool
     */
    function is_module_enabled($module)
    {
        return array_key_exists($module, app('modules')->allEnabled());
    }
}

if (!function_exists('is_core_module')) {
    /**
     * Determine if the given module is core module.
     *
     * @param string $module
     *
     * @return bool
     */
    function is_core_module($module)
    {
        return in_array(strtolower($module), config('fleetcart.modules.core.config.core_modules'));
    }
}

if (!function_exists('slugify')) {
    /**
     * Generate a URL friendly "slug" from a given string
     *
     * @param string $value
     */
    function slugify($value)
    {
        $slug = preg_replace('/[\s<>[\]{}|\\^%&\$,\/:;=?@#\'\"]/', '-', mb_strtolower($value));

        // Remove duplicate separators.
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim special characters from the beginning and end of the slug.
        return trim($slug, '!"#$%&\'()*+,-./:;<=>?@[]^_`{|}~');
    }
}

if (!function_exists('v')) {
    /**
     * Version a relative asset using the time its contents last changed.
     *
     * @param string $value
     *
     * @return string
     */
    function v($path)
    {
        if (config('app.env') === 'local') {
            $version = uniqid();
        } else {
            $version = FleetCart::VERSION;
        }

        return "{$path}?v=" . $version;
    }
}

if (!function_exists('fleetcart_version')) {
    /**
     * Get The SETCOM version.
     *
     * @return string
     */
    function fleetcart_version()
    {
        return FleetCart::VERSION;
    }
}

if (!function_exists('old_json')) {
    /**
     * Retrieve and json encode an old input item.
     *
     * @param string $array
     * @param mixed $default
     * @param mixed $options
     *
     * @return string
     */
    function old_json($key, $default = [], $options = null)
    {
        $old = array_reset_index(old($key, []));

        return json_encode($old ?: $default, $options);
    }
}

if (!function_exists('array_reset_index')) {
    /**
     * Reset numeric index of an array recursively.
     *
     * @param array $array
     *
     * @return array|Collection
     *
     * @see https://stackoverflow.com/a/12399408/5736257
     */
    function array_reset_index($array)
    {
        $array = $array instanceof Collection
            ? $array->toArray()
            : $array;

        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = array_reset_index($val);
            }
        }

        if (isset($key) && is_numeric($key)) {
            return array_values($array);
        }

        return $array;
    }
}

if (!function_exists('html_attrs')) {
    /**
     * Convert array to html attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    function html_attrs(array $attributes)
    {
        $string = '';

        foreach ($attributes as $name => $value) {
            $string .= " {$name}={$value}";
        }

        return $string;
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Convert currency code to currency symbol.
     *
     * @param string $currencyCode
     *
     * @return string
     */

    function currency_symbol(string $currencyCode): string
    {
        return Currencies::getSymbol($currencyCode);
    }
}


if (!function_exists('take_percent')) {
    /**
     * Take the given percent of a given amount.
     *
     * @param int|float $percent
     * @param $amount
     *
     * @return float
     */

    function take_percent($percent, $amount)
    {
        return ($percent / 100) * $amount;
    }
}

if (!function_exists('calculate_percentage')) {
    /**
     * Calculate percentage of an amount in another amount.
     *
     * @param $amount1
     * @param $amount2
     *
     * @return float
     */

    function calculate_percentage($amount1, $amount2)
    {
        return ($amount1 / $amount2) * 100;
    }
}

if (!function_exists('number_format_kmbt')) {
    /**
     * Format a number to K/M/B/T.
     *
     * @param $number
     * @param $precision
     *
     * @return string
     */

    function number_format_kmbt($number, $precision)
    {
        if ($number < 1000) {
            $amount = number_format($number);
        } else if ($number < 1000000) {
            $amount = number_format($number / 1000, $precision) . 'K';
        } else if ($number < 1000000000) {
            $amount = number_format($number / 1000000, $precision) . 'M';
        } else if ($number < 1000000000000) {
            $amount = number_format($number / 1000000000, $precision) . 'B';
        } else {
            $amount = number_format($number / 1000000000000, $precision) . 'T';
        }

        return $amount;
    }
}

if (!function_exists('generate_color_shade')) {
    function generate_color_shade($color,$amount)
    {
        $newColor = new \TinyColor\Color($color);

        $newColor->r = max(round($newColor->r * (1 - $amount)), 0);
        $newColor->g = max(round($newColor->g * (1 - $amount)), 0);
        $newColor->b = max(round($newColor->b * (1 - $amount)), 0);

        return $newColor->toHexString();
    }
}

