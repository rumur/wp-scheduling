<?php

namespace Rumur\WordPress\Scheduling\Concerns;

use Rumur\WordPress\Scheduling\SerializableClosure;

trait CanPrepareSerialization
{
    /**
     * @param mixed $thing   The thing that requires a preparation for a serialization.
     *
     * @return SerializableClosure|SerializableClosure[]
     */
    protected function prepareForSerialization($thing)
    {
        if ($thing instanceof \Closure) {
            return new SerializableClosure($thing);
        }

        if (is_array($thing)) {
            return array_map([$this, 'prepareForSerialization'], $thing);
        }

        return $thing;
    }

    /**
     * @param mixed $thing  The thing that requires a preparation from a serialization.
     *
     * @return mixed
     */
    protected function prepareFromSerialization($thing)
    {
        if ($thing instanceof SerializableClosure) {
            return $thing->getClosure();
        }

        if (is_array($thing)) {
            return array_map([$this, 'prepareFromSerialization'], $thing);
        }

        return $thing;
    }
}
