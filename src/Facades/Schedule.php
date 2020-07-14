<?php

namespace Rumur\WordPress\Scheduling\Facades;

use Illuminate\Support\Facades\Facade;
use Rumur\WordPress\Scheduling\CronTask;
use Rumur\WordPress\Scheduling\Dispatcher;
use Rumur\WordPress\Scheduling\Intervals;
use Rumur\WordPress\Scheduling\PendingTask;
use Rumur\WordPress\Scheduling\Scheduler;
use Rumur\WordPress\Scheduling\WordPressRegistry;

/**
 * Class Schedule
 *
 * @package Rumur\WordPress\Scheduling\Facades
 *
 * @method static Intervals intervals()
 * @method static Dispatcher dispatcher()
 * @method static WordPressRegistry registry()
 * @method static Scheduler resign(CronTask $task, ?string $name = null)
 * @method static Scheduler resignAll(string $name)
 * @method static Scheduler resignAllSingular()
 * @method static Scheduler resignAllRecurrent()
 * @method static Scheduler registerIntoWordPress(array|string[] $tasks = [])
 * @method static Scheduler registerIntervalsIntoWordPress()
 * @method static Scheduler addInterval(string $name, int|array $interval)
 * @method static PendingTask job(object $task)
 * @method static PendingTask call(callable $task, array $args = [])
 */
class Schedule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'rumur.wp.scheduling';
    }
}
