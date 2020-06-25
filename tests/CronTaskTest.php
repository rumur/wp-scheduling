<?php

namespace Rumur\WordPress\Scheduling\Test;

use PHPUnit\Framework\TestCase;
use Rumur\WordPress\Scheduling\CronTask;

class CronTaskTest extends TestCase
{
    public function testCanBeSerializedProperly(): void
    {
        $origin = new CronTask(static function() {
            $variableInside = true;
        }, ['testing' => 2020]);

        $origin->onSuccess(static function() {
           return true;
        });

        $origin->onFailure(static function() {
            return false;
        });

        $serialized = \serialize($origin);

        $this->assertIsString($serialized);

        $this->assertContains('testing', $serialized);
        $this->assertContains('$variableInside', $serialized);

        /** @var CronTask $unserialized */
        $unserialized = \unserialize($serialized);

        $this->assertInstanceOf(CronTask::class, $unserialized);

        $this->assertArrayHasKey('testing', $unserialized->args());

        $this->assertIsCallable($success = $unserialized->successListeners()[0]);
        $this->assertIsCallable($failure = $unserialized->failedListeners()[0]);

        $this->assertTrue($success());
        $this->assertFalse($failure());
    }
}