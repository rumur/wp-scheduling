<?php

namespace Rumur\WordPress\Scheduling\Concerns;

trait HasStatusListeners
{
    /**
     * The list of listeners that fill be fired when the action is failed.
     *
     * @var array
     */
    protected $failedListeners = [];

    /**
     * The list of listeners that fill be fired when the action is successful.
     *
     * @var array
     */
    protected $successListeners = [];

    /**
     * @param callable|callable [] $listeners
     * @return static
     */
    public function onFailure($listeners)
    {
        $listeners = is_array($listeners) ? $listeners : func_get_args();

        $this->failedListeners = array_merge($this->failedListeners, $listeners);

        return $this;
    }

    /**
     * @return array
     */
    public function failedListeners(): array
    {
        return $this->failedListeners;
    }

    /**
     * @param callable|callable [] $listeners
     * @return static
     */
    public function onSuccess($listeners)
    {
        $listeners = is_array($listeners) ? $listeners : func_get_args();

        $this->successListeners = array_merge($this->successListeners, $listeners);

        return $this;
    }

    /**
     * @return array
     */
    public function successListeners(): array
    {
        return $this->successListeners;
    }
}
