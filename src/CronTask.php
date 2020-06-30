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
    protected $performer;

    /**
     * @var string
     */
    protected $interval;

    /**
     * The ExtraTime Is used by a singular tasks.
     *
     * @var int
     */
    protected $extraTime;

    /**
     * The list of attributes that will be stored along with a task.
     *
     * @var array
     */
    protected $args = [];

    /**
     * CronTask constructor.
     *
     * @param mixed $performer The Task Performer
     * @param array $args Args that will be passed to a performer
     * @param string $name The WordPress action name that this task will be fire on.
     * @param string $interval The WordPress Cron Interval this task is using.
     * @param int|null $extraTime  The extra time for a singular task, for a recurrent task it's always `0`.
     */
    public function __construct(
        $performer, array $args = [],
        ?string $name = null, ?string $interval = null, int $extraTime = 0
    )
    {
        $this->args = $args;
        $this->name = $name;
        $this->interval = $interval;
        $this->extraTime = $extraTime;
        $this->performer = $performer;
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
     * Gets an extraTime the task is using.
     *
     * @return int
     */
    public function extraTime(): int
    {
        return $this->extraTime;
    }

    /**
     * Sets an extraTime for the task.
     *
     * @param int $time
     *
     * @return static
     */
    public function useExtraTime(int $time)
    {
        $this->extraTime = $time;

        return $this;
    }

    /**
     * Gets interval the task is using.
     *
     * @return null|string
     */
    public function interval(): ?string
    {
        return $this->interval;
    }

    /**
     * Sets the interval for the task.
     *
     * @param string $interval
     * @return static
     */
    public function useInterval(string $interval)
    {
        $this->interval = $interval;

        return $this;
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
     * Gets the task performer instance.
     *
     * @return mixed
     */
    public function performer()
    {
        return $this->performer;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'args' => $this->args,
            'interval' => $this->interval,
            'extraTime' => $this->extraTime,
            'performer' => $this->performer,
            'failedListeners' => $this->failedListeners,
            'successListeners' => $this->successListeners,
        ];
    }

    /**
     * String representation of object.
     *
     * @return string
     */
    public function serialize(): string
    {
        return \serialize(
            (object)$this->prepareForSerialization($this->toArray())
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

        $properties = array_keys($this->toArray());

        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->{$property} = $this->prepareFromSerialization($data->{$property});
            }
        }
    }
}