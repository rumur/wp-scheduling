<?php

namespace Rumur\WordPress\Scheduling;

class PendingTask
{
    use Concerns\HasSingleRecurrence;
    use Concerns\HasMultipleRecurrence;

    /** @var CronTask */
    protected $task;

    /** @var Dispatcher */
    protected $dispatcher;

    /** @var Intervals */
    protected $interval;

    /** @var WordPressRegistry */
    protected $registry;

    /**
     * PendingTask constructor.
     *
     * @param CronTask $task
     * @param Intervals $interval
     * @param WordPressRegistry $registry
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        CronTask $task,
        Intervals $interval,
        WordPressRegistry $registry,
        Dispatcher $dispatcher
    ) {
        $this->task = $task;
        $this->interval = $interval;
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param callable $listener
     * @return static
     */
    public function onFailure(callable $listener)
    {
        $this->task->onFailure($listener);

        return $this;
    }

    /**
     * @param callable $listener
     * @return static
     */
    public function onSuccess(callable $listener)
    {
        $this->task->onSuccess($listener);

        return $this;
    }

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
        $task = $this->resolveTask()->useInterval($interval);

        // In order to have one task, but several events
        // in this case we need to add a specific time mark for a task.
        $task->useExtraTime($extraTime);

        if (! $task->name()) {
            $task->useName($this->registry->singularActionName());
        }

        if (! $timestamp && $this->interval->has($interval) && $this->registry->isNotRegistered($task)) {
            $this->registry->scheduleSingular(
                $task,
                $this->interval->calculateFromNow($interval, $extraTime)
            );
        }

        if ($timestamp && $this->registry->isNotRegistered($task)) {
            $this->registry->scheduleSingular($task, $timestamp);
        }

        return $task;
    }

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
        $task = $this->resolveTask()->useInterval($interval);

        if (! $task->name()) {
            $task->useName($this->registry->recurrentActionName());
        }

        if ($this->interval->has($interval) && $this->registry->isNotRegistered($task)) {
            $this->registry->scheduleRecurrence($task, $timestamp ?: $this->interval->now(), $interval);
        }

        return $task;
    }

    /**
     * @return CronTask
     */
    public function resolveTask(): CronTask
    {
        return clone $this->task;
    }

    /**
     * @param $data
     *
     * @return static
     */
    public function with($data)
    {
        $data = is_array($data) ? $data : func_get_args();

        $this->task->useArgs($data);

        return $this;
    }

    /**
     * Run's the Task right now, but if it was scheduled it keeps it.
     *
     * This method was designed in order to debug a current task.
     *
     * It mimics the behavior of a WordPress cron.
     *
     * @return CronTask
     */
    public function runNow(): CronTask
    {
        call_user_func($this->dispatcher, $this->task);

        return $this->resolveTask();
    }

    /**
     * May be used to ping a given URL only if the task succeeds.
     *
     * @param string $successUrl
     *
     * @return static
     */
    public function pingOnSuccess(string $successUrl)
    {
        $this->onSuccess(static function () use ($successUrl) {
            // Needed to mention the whole namespace in order
            // to avoid an error, that class `Pinger` not found.
            // It happens due to serialization process.
            // The context of the class can be bound within serialization by adding a full namespace directly.
            (new \Rumur\WordPress\Scheduling\Pinger())->ping($successUrl);
        });

        return $this;
    }

    /**
     * May be used to ping a given URL only if the task fails.
     *
     * @param string $failureUrl
     *
     * @return static
     */
    public function pingOnFailure(string $failureUrl)
    {
        $this->onFailure(static function () use ($failureUrl) {
            // Needed to mention the whole namespace in order
            // to avoid an error, that class `Pinger` not found.
            // It happens due to serialization process.
            // The context of the class can be bound within serialization by adding a full namespace directly.
            (new \Rumur\WordPress\Scheduling\Pinger())->ping($failureUrl);
        });

        return $this;
    }
}
