<?php

namespace Rumur\WordPress\Scheduling;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class WordPressScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register schedule services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('rumur.wp.scheduling', function () {
            $name = Str::snake($this->app->make('config')->get('app.name', 'scheduled'));

            new Scheduler(
                new Intervals(),
                new Dispatcher(),
                new WordPressRegistry("{$name}_singular", "{$name}_recurrent")
            );
        });
    }
}
