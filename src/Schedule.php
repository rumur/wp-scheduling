<?php

namespace Rumur\WordPress\Scheduling;

/**
 * Class Schedule
 *
 * @package Rumur\WordPress\Scheduling
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
 * @method static PendingTask call(callable $task, array $args = [], ?string $name = null)
 */
class Schedule
{
    /**
     * @var Scheduler
     */
    protected $scheduler;

    /**
     * @var Schedule
     */
    protected static $instance;

    /**
     * Schedule constructor.
     *
     * @param Scheduler $scheduler
     */
    protected function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * Facade that just is proxying to Scheduler.
     *
     * @param $method
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($method, $arguments)
    {
        if (!static::$instance) {
            static::$instance = new static(
                new Scheduler(new Intervals, new Dispatcher, new WordPressRegistry)
            );
        }

        if (! method_exists(static::$instance->scheduler, $method)) {
            throw new \InvalidArgumentException(sprintf('The `%s` method not exists', $method));
        }

        return static::$instance->scheduler->$method(...$arguments);
    }
}