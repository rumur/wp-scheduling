<?php

namespace Rumur\WordPress\Scheduling\Concerns;

use Rumur\WordPress\Scheduling\CronTask;

trait HasSingleRecurrence
{
    /**
     * Run a Task one time in minutes.
     *
     * @param int $min How many minutes before performing a task.
     * @return CronTask
     */
    public function runOnceInMinutes(int $min): CronTask
    {
        return $this->registerSingular('minute', $min);
    }

    /**
     * Run a Task one time in hours.
     *
     * @param int $hours How many hours before performing a task.
     * @return CronTask
     */
    public function runOnceInHours(int $hours): CronTask
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
    public function runOnceInDays(int $days): CronTask
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
    public function runOnceInWeeks(int $weeks): CronTask
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
    public function runOnceInMonths(int $months): CronTask
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
    public function runOnceInQuarters(int $quarters): CronTask
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
