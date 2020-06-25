<?php

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('\\do_action')):
    function do_action($tag, ...$arg)
    {
    }
endif;

if (!function_exists('\\apply_filters')):
    function apply_filters($tag, $value)
    {
        return $value;
    }
endif;

if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
    define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
    define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
    define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
    define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
    define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);
}