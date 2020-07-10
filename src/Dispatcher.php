<?php

namespace Rumur\WordPress\Scheduling;

class Dispatcher
{
    /**
     * The method that will be called in the class instances.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * @param CronTask $task Unserialized CronTask
     *
     * @throws \Throwable
     */
    public function __invoke(CronTask $task)
    {
        $args = $task->args();

        $onSuccess = $task->successListeners();
        $onFailure = $task->failedListeners();

        try {
            $this->dispatch($task->performer(), $args);
            $this->performListeners($onSuccess, $task, $args);
        } catch (\Throwable $e) {
            $this->performListeners($onFailure, $task, $args, $e->getMessage());
        }
    }

    /**
     * Dispatching the task.
     *
     * @param mixed $performer
     * @param array $args
     *
     * @uses \do_action
     *
     * @throws \Throwable
     */
    public function dispatch($performer, array $args = []): void
    {
        if ($withWordPress = function_exists('\\do_action')) {
            \do_action('rumur/scheduling/dispatch/before', $performer, $args);
        }

        // If the instance is not invokable
        // we gonna try to call it via the `method` then.
        if (is_object($performer) && !is_callable($performer)) {
            $performer = [$performer, $this->method];
        }

        if (is_callable($performer)) {
            call_user_func($performer, $args);
        } else {
            throw new \InvalidArgumentException('Could not dispatch the task, it should be callable one.');
        }

        if ($withWordPress) {
            \do_action('rumur/scheduling/dispatch/after', $performer, $args);
        }
    }

    /**
     * Changes the method that need to be executed.
     *
     * @param string $method
     *
     * @return static
     */
    public function via(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param array $listeners
     * @param CronTask $task
     * @param array $args
     * @param array $extra
     * @return static
     */
    protected function performListeners(array $listeners, CronTask $task, array $args = [], ...$extra)
    {
        $should_proceed = true;

        foreach ($listeners as $listener) {
            // Stop of calling next listeners.
            if (false === $should_proceed) {
                break;
            }

            if (is_string($listener) && class_exists($listener)) {
                $l = new $listener();

                if (method_exists($l, 'handle')) {
                    $should_proceed = $l->handle($task, $args, ...$extra);
                }
            }

            if (is_callable($listener)) {
                $should_proceed = call_user_func($listener, $task, $args, ...$extra);
            }
        }

        return $this;
    }
}
