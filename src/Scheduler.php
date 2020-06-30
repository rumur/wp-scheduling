<?php

namespace Rumur\WordPress\Scheduling;

class Scheduler
{
    /**
     * @var Intervals
     */
    protected $interval;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var WordPressRegistry
     */
    protected $registry;

    /**
     * Scheduler constructor.
     *
     * @param Intervals $interval
     * @param Dispatcher $dispatcher
     * @param WordPressRegistry $registry
     */
    public function __construct(Intervals $interval, Dispatcher $dispatcher, WordPressRegistry $registry)
    {
        $this->interval = $interval;
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Adds an interval.
     *
     * @param string $name
     * @param int|array $interval   Can be an interval in seconds or a WordPress format interval.
     *
     * @return static
     */
    public function addInterval(string $name, $interval)
    {
        $this->intervals()->add($name, $interval);

        return $this;
    }

    /**
     * Adds a task into WordPress.
     *
     * @param callable $task
     *
     * @param array $args
     * @return PendingTask
     */
    public function call(callable $task, array $args = []): PendingTask
    {
        return $this->makePendingTask($task, $args);
    }

    /**
     * Adds a task into WordPress.
     *
     * @param object $task
     *
     * @return PendingTask
     */
    public function job($task): PendingTask
    {
        return $this->makePendingTask($task);
    }

    /**
     * @return Dispatcher
     */
    public function dispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    /**
     * @return Intervals
     */
    public function intervals(): Intervals
    {
        return $this->interval;
    }

    /**
     * @return WordPressRegistry
     */
    public function registry(): WordPressRegistry
    {
        return $this->registry;
    }

    /**
     * Deletes the job from the WordPress queue.
     *
     * @param CronTask $task
     * @param string|null $hook
     * @return static
     */
    public function resign(CronTask $task, ?string $hook = null)
    {
        $this->registry->resign($task, $hook);

        return $this;
    }

    /**
     * Deletes All jobs from the WordPress queue.
     * Use this method Cautiously.
     *
     * It was designed for uninstall use.
     *
     * @param string $hook
     * @return static
     */
    public function resignAll(string $hook)
    {
        $this->registry->resignAll($hook);

        return $this;
    }

    /**
     * Deletes All singular jobs from the WordPress queue.
     *
     * @return static
     */
    public function resignAllSingular()
    {
        $this->registry->resignAll($this->registry->singularActionName());

        return $this;
    }

    /**
     * Deletes All recurrent jobs from the WordPress queue.
     *
     * @return static
     */
    public function resignAllRecurrent()
    {
        $this->registry->resignAll($this->registry->recurrentActionName());

        return $this;
    }

    /**
     * Registers all tasks into WordPress.
     *
     * @param string[] $tasks   The list with Jobs Classes.
     * @uses \add_action
     *
     * @return static
     */
    public function registerIntoWordPress($tasks = [])
    {
        $tasks = is_array($tasks) ? $tasks : func_get_args();

        \add_action($this->registry->singularActionName(), $this->dispatcher, 10, 2);
        \add_action($this->registry->recurrentActionName(), $this->dispatcher, 10, 2);

        foreach ($tasks as $task) {
            \add_action($this->taskToActionName($task), $this->dispatcher, 10, 2);
        }

        $this->registerIntervalsIntoWordPress();

        return $this;
    }

    /**
     * Registers intervals.
     *
     * @return static
     */
    public function registerIntervalsIntoWordPress()
    {
        $this->intervals()->registerOnAction('init');

        return $this;
    }

    /**
     * @param $task
     * @param array $args
     * @param string|null $hook
     *
     * @uses \has_action
     *
     * @return PendingTask
     */
    protected function makePendingTask($task, array $args = [], ?string $hook = null): PendingTask
    {
        // If there's no a specific hook and the task is not a closure
        // and can be converted to a hook name, so we can try to create it.
        if (! $hook && false === $task instanceof \Closure && !is_array($task)) {
            // This is for those custom tasks that has been registered via @see `static::registerIntoWordPress`
            // and they have registered their specific names already.
            if (\has_action($name = $this->taskToActionName($task))) {
                $hook = $name;
            }
        }

        return (new PendingTask(
            new CronTask($task, $args, $hook),
            $this->interval,
            $this->registry,
            $this->dispatcher
        ));
    }

    /**
     * Creates a hook name based on passed task.
     *
     * @param object|string $task
     *
     * @uses \sanitize_title_with_dashes
     *
     * @return string
     */
    protected function taskToActionName($task): string
    {
        $class = $name = false;

        if (is_object($task)) {
            $class = get_class($task);
        }

        if (is_string($task) && class_exists($task)) {
            $class = $task;
        }

        if ($class) {
            $name = basename(str_replace('\\', '/', $class));
        }

        if (! $name && is_string($task)) {
            $name = $task;
        }

        if (! $name) {
            throw new \InvalidArgumentException('Could not convert the task into a valid action name.');
        }

        return \sanitize_title_with_dashes($name);
    }
}