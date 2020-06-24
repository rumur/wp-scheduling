<?php

namespace Rumur\WordPress\Scheduling;

class CronTask implements \Serializable
{
    use Concerns\HasStatusListeners,
        Concerns\CanPrepareSerialization;

    /**
     * The Name of the task's hook.
     *
     * @var string
     */
    protected $name;

    /**
     * The Task that will be stored and after a time performed.
     *
     * @var mixed
     */
    protected $task;

    /**
     * The list of attributes that will be stored along with a task.
     *
     * @var array
     */
    protected $args = [];

    /**
     * CronTask constructor.
     *
     * @param mixed $task
     * @param array $args
     * @param string $name
     */
    public function __construct($task, array $args = [], ?string $name = null)
    {
        $this->task = $task;
        $this->args = $args;
        $this->name = $name;
    }

    /**
     * Gets the task performer instance.
     *
     * @return mixed
     */
    public function task()
    {
        return $this->task;
    }

    /**
     * Gets the name of the action hook that this task will be fired on.
     *
     * If the name is `null` it means that the task is not been registered yet.
     *
     * @return null|string
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * Sets a hook name this task is gonna fired on.
     *
     * @internal
     *
     * @param string $hook
     * @return static
     */
    public function useName(string $hook)
    {
        $this->name = $hook;

        return $this;
    }

    /**
     * Gets args for the task.
     *
     * @return array
     */
    public function args(): array
    {
        return $this->args;
    }

    /**
     * Sets args for a task.
     *
     * @param array $args
     * @return static
     */
    public function useArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @return string
     */
    public function serialize(): string
    {
        return \serialize(
            (object)$this->prepareForSerialization([
                'name' => $this->name,
                'task' => $this->task,
                'args' => $this->args,
                'failedListeners' => $this->failedListeners,
                'successListeners' => $this->successListeners,
            ])
        );
    }

    /**
     * Constructs the object back.
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized);

        $this->name = $this->prepareFromSerialization($data->name);
        $this->task = $this->prepareFromSerialization($data->task);
        $this->args = $this->prepareFromSerialization($data->args);

        $this->failedListeners = $this->prepareFromSerialization($data->failedListeners);
        $this->successListeners = $this->prepareFromSerialization($data->successListeners);
    }
}