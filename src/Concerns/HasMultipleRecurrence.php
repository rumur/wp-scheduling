<?php

namespace Rumur\WordPress\Scheduling\Concerns;

use Rumur\WordPress\Scheduling\CronTask;

trait HasMultipleRecurrence
{
    /**
     * Adds a recurrent Task to a system queue.
     *
     * @param string $interval Available type of scheduling
     * @param int|null $timestamp The calculated $timestamp or by default it is `now`
     *
     * @return CronTask
     */
    public function registerRecurrence(string $interval, ?int $timestamp = null): CronTask
    {
        if ($this->interval->has($interval) && $this->registry->isRecurrenceNotRegistered($this->task)) {
            $this->registry->scheduleRecurrence($this->task, $timestamp ?: $this->interval->now(), $interval);
        }

        // Adds a hook name for a task if was registered with.
        if (! $this->task->name()) {
            $this->task->useName($this->registry->recurrentActionName());
        }

        return $this->resolveTask();
    }

    /**
     * Run the task every minute
     *
     * @return CronTask
     */
    public function runEveryMinute(): CronTask
    {
        return $this->registerRecurrence('every-minute');
    }

    /**
     * Run the task every five minutes
     *
     * @return CronTask
     */
    public function runEveryFiveMinutes(): CronTask
    {
        return $this->registerRecurrence('every-five-minutes');
    }

    /**
     * Run the task every ten minutes
     *
     * @return CronTask
     */
    public function runEveryTenMinutes(): CronTask
    {
        return $this->registerRecurrence('every-ten-minutes');
    }

    /**
     * Run the task every 15 minutes
     *
     * @return CronTask
     */
    public function runEveryFifteenMinutes(): CronTask
    {
        return $this->registerRecurrence('every-fifteen-minutes');
    }

    /**
     * Run the task every 30 minutes
     *
     * @return CronTask
     */
    public function runEveryThirtyMinutes(): CronTask
    {
        return $this->registerRecurrence('every-thirty-minutes');
    }

    /**
     * Run the task every hour
     *
     * @return CronTask
     */
    public function runHourly(): CronTask
    {
        return $this->registerRecurrence('hourly');
    }

    /**
     * Run the task every day at midnight
     *
     * @return CronTask
     */
    public function runDaily(): CronTask
    {
        return $this->registerRecurrence('daily');
    }

    /**
     * Run the task every week.
     *
     * @return CronTask
     */
    public function runWeekly(): CronTask
    {
        return $this->registerRecurrence('weekly');
    }

    /**
     * Run the task every month.
     *
     * @return CronTask
     */
    public function runMonthly(): CronTask
    {
        return $this->registerRecurrence('monthly');
    }

    /**
     * Run the task every quarter.
     *
     * @return CronTask
     */
    public function runQuarterly(): CronTask
    {
        return $this->registerRecurrence('quarterly');
    }

    /**
     * Run the task every year
     *
     * @return CronTask
     */
    public function runYearly(): CronTask
    {
        return $this->registerRecurrence('yearly');
    }
}