<?php

namespace Rumur\WordPress\Scheduling;

use Opis\Closure\ClosureScope;
use Opis\Closure\SerializableClosure as OpisSerializableClosure;

class SerializableClosure extends OpisSerializableClosure
{
    /**
     * Implementation of Serializable::serialize()
     *
     * @return  string  The serialized closure
     */
    public function serialize(): string
    {
        if ($this->scope === null) {
            $this->scope = new ClosureScope();
            $this->scope->toserialize++;
        }

        $this->scope->serializations++;

        $scope = $object = null;
        $reflector = $this->getReflector();

        if($reflector->isBindingRequired()){
            $object = $reflector->getClosureThis();
            static::wrapClosures($object, $this->scope);
            if($scope = $reflector->getClosureScopeClass()){
                $scope = $scope->name;
            }
        } else {
            if($scope = $reflector->getClosureScopeClass()){
                $scope = $scope->name;
            }
        }

        $this->reference = spl_object_hash($this->closure);

        $this->scope[$this->closure] = $this;

        $use = $this->transformUseVariables($reflector->getUseVariables());
        $code = $reflector->getCode();

        $this->mapByReference($use);

        // All staff with `reference` has been done, so we can ignore it further,
        // Here is why.
        // When SerializableClosure has been serialized it creates a hash that belongs to the session
        // and this hash is needed only when the object is gonna be unserialized within this session,
        // however this object will be store to the DB and restored later when the task's time has come.
        // So we need to delete this hash otherwise the same task with same args will be always treated as a new one
        // because a WordPress creates its own md5 hash from a serialized string
        // and this string won't match due to the its session hash.

        $ret = \serialize(array(
            'use' => $use,
            'function' => $code,
            'scope' => $scope,
            'this' => $object,
            'self' => '',//$this->reference,
        ));

        if (static::$securityProvider !== null) {
            $data = static::$securityProvider->sign($ret);
            $ret =  '@' . $data['hash'] . '.' . $data['closure'];
        }

        if (!--$this->scope->serializations && !--$this->scope->toserialize) {
            $this->scope = null;
        }

        return $ret;
    }
}