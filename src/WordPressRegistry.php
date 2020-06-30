<?php

namespace Rumur\WordPress\Scheduling;

class WordPressRegistry
{
    /**
     * The name of a default single action name.
     *
     * Under this name will be fired all singular tasks that were registered without a specific hook name.
     *
     * @var string
     */
    protected $singularName = 'rumur_scheduled_singular';

    /**
     * The name of a default multiple action name.
     *
     * Under this name will be fired all singular tasks that were registered without a specific hook name.
     *
     * @var string
     */
    protected $recurrentName = 'rumur_scheduled_recurrence';

    /**
     * @return string
     */
    public function singularActionName(): string
    {
        return $this->singularName;
    }

    /**
     * @return string
     */
    public function recurrentActionName(): string
    {
        return $this->recurrentName;
    }

    /**
     * Checks if task is not registered yet.
     *
     * @param CronTask $task
     * @param string|null $hookName
     * @return bool
     */
    public function isNotRegistered(CronTask $task, ?string $hookName = null): bool
    {
        return !$this->isRegistered($task, $hookName);
    }

    /**
     * Checks if a task is already registered.
     *
     * @param CronTask $task
     * @param string|null $hookName
     * @return bool
     *
     * @uses \wp_next_scheduled
     */
    public function isRegistered(CronTask $task, ?string $hookName = null): bool
    {
        if (! $hookName = $hookName ?: $task->name()) {
            throw new \InvalidArgumentException('The Task does not have a name: ' . print_r($task, true));
        }

        return (bool)\wp_next_scheduled($hookName, [$task]);
    }

    /**
     * Schedules an event to run only once.
     *
     * Schedules a hook which will be triggered by WordPress at the specified time.
     * The action will trigger when someone visits your WordPress site if the scheduled
     * time has passed.
     *
     * Note that scheduling an event to occur within 10 minutes of an existing event
     * with the same action hook will be ignored unless you pass unique `$args` values
     * for each scheduled event.
     *
     * @param int $timestamp Unix timestamp (UTC) for when to next run the event.
     * @param CronTask $task The task that gonna to fire single one.
     *
     * @uses \wp_schedule_single_event
     *
     * @return static
     */
    public function scheduleSingular(CronTask $task, int $timestamp)
    {
        \wp_schedule_single_event($timestamp, $task->name() ?: $this->singularActionName(), [$task]);

        return $this;
    }

    /**
     * Schedules a recurring event.
     *
     * Schedules a hook which will be triggered by WordPress at the specified interval.
     * The action will trigger when someone visits your WordPress site if the scheduled
     * time has passed.
     *
     * Valid values for the interval are 'hourly', 'daily', and 'twicedaily'. These can
     * be extended using the {@see 'cron_schedules'} filter in wp_get_schedules().
     *
     * Note that scheduling an event to occur within 10 minutes of an existing event
     * with the same action hook will be ignored unless you pass unique `$args` values
     * for each scheduled event.
     *
     * @param CronTask $task The Task that will be executed later.
     * @param int $timestamp Unix timestamp (UTC) for when to next run the event.
     * @param string $interval The name of an interval.
     *
     * @return static
     * @uses \wp_schedule_event
     *
     */
    public function scheduleRecurrence(CronTask $task, int $timestamp, string $interval)
    {
        \wp_schedule_event($timestamp, $interval,
            $task->name() ?: $this->recurrentActionName(), [$task]);

        return $this;
    }

    /**
     * Resign a particular job
     *
     * @param CronTask $task
     * @param string|null $hook
     *
     * @return static
     * @uses \wp_clear_scheduled_hook
     */
    public function resign(CronTask $task, ?string $hook = null)
    {
        $isRegistered = $this->isRegistered($task,
            $hookName = $hook ?: $task->name() ?: $this->singularActionName()
        );
        
        if (! $isRegistered) {
            $isRegistered = $this->isRegistered($task,
                $hookName = $this->recurrentActionName()
            );
        }

        if ($isRegistered) {
            \wp_clear_scheduled_hook($hookName, [$task]);
        }

        return $this;
    }

    /**
     * Resign all jobs for a provided hook.
     *
     * @param string $hook
     *
     * @uses \wp_unschedule_hook
     *
     * @return static
     */
    public function resignAll(string $hook)
    {
        \wp_unschedule_hook($hook);

        return $this;
    }
}