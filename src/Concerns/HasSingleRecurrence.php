<?php

namespace Rumur\WordPress\Scheduling\Concerns;

use Rumur\WordPress\Scheduling\CronTask;

trait HasSingleRecurrence
{
    /**
     * Adds a Task to a system queue that will be run one time only.
     *
     * @param string $interval Available type of scheduling
     * @param int $extraTime The extra time that need to be adjusted.
     * @param int|null $timestamp The calculated $timestamp
     *
     * @return CronTask
     */
    public function registerSingular(string $interval, int $extraTime = 1, ?int $timestamp = null): CronTask
    {
        if ($this->interval->has($interval) && $this->registry->isSingularNotRegistered($this->task)) {

            $calculated = $timestamp ?: $this->interval->calculateFromNow($interval, $extraTime);

            $this->registry->scheduleSingular($this->task, $calculated);
        }

        return $this->resolveTask();
    }

    /**
     * Run a Task one time in minutes.
     *
     * @param int $min How many minutes before performing a task.
     * @return CronTask
     */
    public function runOnceInMinutes($min): CronTask
    {
        return $this->registerSingular('minute', $min);
    }

    /**
     * Run a Task one time in hours.
     *
     * @param int $hours How many hours before performing a task.
     * @return CronTask
     */
    public function runOnceInHours($hours): CronTask
    {
        return $this->registerSingular('hour', $hours);
    }

    /**
     * Run a Task one time in days.
     *
     * @param int $days How many days before performing a task.
     *
     * @return CronTask
     */
    public function runOnceInDays($days): CronTask
    {
        return $this->registerSingular('day', $days);
    }

    /**
     * Run a Task one time in weeks.
     *
     * @param int $weeks How many weeks before performing a task.
     *
     * @return CronTask
     */
    public function runOnceInWeeks($weeks): CronTask
    {
        return $this->registerSingular('week', $weeks);
    }

    /**
     * Run a Task one time in months.
     *
     * @param int $months How many months before performing a task.
     *
     * @return CronTask
     */
    public function runOnceInMonths($months): CronTask
    {
        return $this->registerSingular('month', $months);
    }

    /**
     * Run a Task one time in quarters.
     *
     * @param int $quarters How many quarters before performing a task
     *
     * @return CronTask
     */
    public function runOnceInQuarters($quarters): CronTask
    {
        return $this->registerSingular('quarter', $quarters);
    }

    /**
     * Run a Task one time in a year.
     *
     * @return CronTask
     */
    public function runOnceInYear(): CronTask
    {
        return $this->registerSingular('year');
    }

    /**
     * Run a Task one time in a minute.
     *
     * @return CronTask
     */
    public function runOnceInMinute(): CronTask
    {
        return $this->runOnceInMinutes(1);
    }

    /**
     * Run a Task one time in five minutes
     *
     * @return CronTask
     */
    public function runOnceInFiveMinutes(): CronTask
    {
        return $this->runOnceInMinutes(5);
    }

    /**
     * Run a Task one time in ten minutes.
     *
     * @return CronTask
     */
    public function runOnceInTenMinutes(): CronTask
    {
        return $this->runOnceInMinutes(10);
    }

    /**
     * Run a Task one time in 15 minutes.
     *
     * @return CronTask
     */
    public function runOnceInFifteenMinutes(): CronTask
    {
        return $this->runOnceInMinutes(15);
    }

    /**
     * Run a Task one time in 30 minutes.
     *
     * @return CronTask
     */
    public function runOnceInThirtyMinutes(): CronTask
    {
        return $this->runOnceInMinutes(30);
    }

    /**
     * Run a Task one time in one hour.
     *
     * @return CronTask
     */
    public function runOnceInHour(): CronTask
    {
        return $this->runOnceInHours(1);
    }

    /**
     * Run a Task one time in one day.
     *
     * @return CronTask
     */
    public function runOnceInDay(): CronTask
    {
        return $this->runOnceInDays(1);
    }

    /**
     * Run a Task one time in a week.
     *
     * @return CronTask
     */
    public function runOnceInWeek(): CronTask
    {
        return $this->runOnceInWeeks(1);
    }

    /**
     * Run a Task one time in a month.
     *
     * @return CronTask
     */
    public function runOnceInMonth(): CronTask
    {
        return $this->runOnceInMonths(1);
    }

    /**
     * Run a Task one time in a quarter.
     *
     * @return CronTask
     */
    public function runOnceInQuarter(): CronTask
    {
        return $this->runOnceInQuarters(1);
    }
}