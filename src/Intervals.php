<?php

namespace Rumur\WordPress\Scheduling;

class Intervals
{
    /**
     * Indicates that intervals has been registered to the system.
     *
     * @var bool
     */
    protected $isRegistered = false;

    /**
     * The Multiple recurrence intervals.
     *
     * @var array
     */
    protected $recurrent = [
        'every-minute' => MINUTE_IN_SECONDS,
        'every-five-minutes' => 5 * MINUTE_IN_SECONDS,
        'every-ten-minutes' => 10 * MINUTE_IN_SECONDS,
        'every-fifteen-minutes' => 15 * MINUTE_IN_SECONDS,
        'every-thirty-minutes' => 30 * MINUTE_IN_SECONDS,
        'hourly' => HOUR_IN_SECONDS,
        'daily' => DAY_IN_SECONDS,
        'weekly' => WEEK_IN_SECONDS,
        'monthly' => MONTH_IN_SECONDS,
        'quarterly' => 3 * MONTH_IN_SECONDS,
        'yearly' => YEAR_IN_SECONDS,
    ];

    /**
     * The Single recurrence intervals.
     *
     * @var array
     */
    protected $singular = [
        'minute' => MINUTE_IN_SECONDS,
        'hour' => HOUR_IN_SECONDS,
        'day' => DAY_IN_SECONDS,
        'week' => WEEK_IN_SECONDS,
        'month' => MONTH_IN_SECONDS,
        'quarter' => 3 * MONTH_IN_SECONDS,
        'year' => YEAR_IN_SECONDS,
    ];

    /**
     * The custom intervals.
     *
     * @var array
     */
    protected $additional = [];

    /**
     * Adds an interval to the registry.
     *
     * @param string $intervalName
     * @param int|array $intervalTime  Can be an interval in seconds or a WordPress format interval.
     * @return static
     */
    public function add(string $intervalName, $intervalTime)
    {
        if ($this->isRegistered) {
            throw new \RuntimeException('An interval could only be added before Intervals have been registered into WordPress.');
        }

        $this->additional[$intervalName] = $intervalTime;

        return $this;
    }

    /**
     * Checks whether it has or not the specific interval.
     *
     * @param string $intervalName
     *
     * @return bool
     */
    public function has(string $intervalName): bool
    {
        return isset($this->all()[$intervalName]);
    }

    /**
     * Retrieves the interval if has one, otherwise an empty array.
     *
     * @param string $intervalName
     * @return array
     */
    public function get(string $intervalName): array
    {
        if (! $this->has($intervalName)) {
            return [];
        }

        return $this->asWordPressInterval([
            $intervalName => $this->all()[$intervalName]
        ])[$intervalName];
    }

    /**
     * Retrieves the gathered list of all intervals.
     *
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->singular, $this->recurrent, $this->additional);
    }

    /**
     * @param string $intervalName
     * @param int $extraTime
     *
     * @return int
     */
    public function calculateFromNow(string $intervalName, int $extraTime): int
    {
        $available = MINUTE_IN_SECONDS;

        if ($this->has($intervalName)) {

            $available = $this->get($intervalName);

            $available = $available['interval'];
        }

        return $this->now() + ($available * $extraTime);
    }

    /**
     * Retrieves the current timestamp.
     *
     * @uses \apply_filters
     *
     * @return int  Timestamp of current time.
     */
    public function now(): int
    {
        return \apply_filters('rumur/scheduling/intervals/now', time(), $this);
    }

    /**
     * Makes intervals be available for the WordPress by hooking on a specific action
     *
     * @uses \current_action()
     * @uses \did_action()
     * @uses \add_action()
     * @param string $action
     * @param int $priority
     *
     * @return static
     */
    public function registerOnAction(string $action = 'init', int $priority = 10)
    {
        if (\current_action() === $action || \did_action($action)) {
            $this->register();
        } else {
            \add_action($action, function () {
                $this->register();
            }, $priority);
        }

        return $this;
    }

    /**
     * Makes intervals be available for the WordPress.
     *
     * @uses \add_filter
     *
     * @return static
     */
    public function register()
    {
        /**
         * Adds the non-default cron schedules.
         *
         * The hook is described in /wp-includes/cron.php
         *
         * @param array $schedules An array of non-default cron schedules.
         *
         * @return array
         */
        \add_filter('cron_schedules', [$this, 'registrar'], 10);

        return $this;
    }

    /**
     * Unregisters intervals from the WordPress.
     *
     * @uses \remove_filter
     *
     * @return bool
     */
    public function unregister(): bool
    {
        if ($isRemoved = \remove_filter('cron_schedules', [$this, 'registrar'], 10)) {
            $this->isRegistered = false;
        }

        return $isRemoved;
    }

    /**
     * Converts intervals into and array that WordPress could understand.
     *
     * @uses \apply_filters
     *
     * @return array
     */
    public function toWordPressArray(): array
    {
        $recurrent = \apply_filters('rumur/scheduling/intervals/recurrent',
            $this->asWordPressInterval($this->recurrent, null, '-')
        );

        $additional = \apply_filters('rumur/scheduling/intervals/additional',
            $this->asWordPressInterval($this->additional)
        );

        return \apply_filters('rumur/scheduling/intervals', array_merge($recurrent, $additional));
    }

    /**
     * Registrar that registers custom intervals to the system.
     *
     * @param array $schedules
     *
     * @return array
     */
    public function registrar(array $schedules): array
    {
        $this->isRegistered = true;

        return array_merge($this->toWordPressArray(), $schedules);
    }

    /**
     * Prepares interval's format to be digestible by a WordPress.
     *
     * @param array $target
     * @param string|null $prepend
     * @param string $delimiter
     * @return array
     */
    protected function asWordPressInterval(array $target, ?string $prepend = null, ?string $delimiter = null): array
    {
        $prepared = [];

        foreach ($target as $name => $interval) {

            // It should be already ready for use.
            if (is_array($interval)) {
                $prepared[$name] = $interval;
                continue;
            }

            // Try to detect the delimiter automatically
            if (! $delimiter) {

                if (strpos($name, '_') !== false) {
                    $delimiter = '_';
                } else {
                    $delimiter = '-';
                }
            }

            $pieces = explode($delimiter, $name);

            if ($prepend) {
                array_unshift($pieces, $prepend);
            }

            $prepared[$name] = [
                'interval' => $interval,
                'display' => implode(' ',
                    array_map('ucfirst', $pieces)
                )
            ];
        }

        return $prepared;
    }
}