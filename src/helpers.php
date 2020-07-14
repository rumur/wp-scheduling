<?php

use Rumur\WordPress\Scheduling\Dispatcher;
use Rumur\WordPress\Scheduling\Intervals;
use Rumur\WordPress\Scheduling\Scheduler;
use Rumur\WordPress\Scheduling\WordPressRegistry;

if (! function_exists('schedule')) {
    /**
     * Get Scheduler instance.
     *
     * @return \Rumur\WordPress\Scheduling\Scheduler
     */
    function schedule()
    {
        if (function_exists('app')) {
            return app('rumur.wp.scheduling');
        }

        return new Scheduler(new Intervals(), new Dispatcher(), new WordPressRegistry());
    }
}
