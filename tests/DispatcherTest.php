<?php

namespace Rumur\WordPress\Scheduling\Test;

use PHPUnit\Framework\TestCase;
use Rumur\WordPress\Scheduling\CronTask;
use Rumur\WordPress\Scheduling\Dispatcher;

class DispatcherTest extends TestCase
{
    public function testCanDispatchSuccessfully(): void
    {
        $origin = new CronTask(static function() {
            $variableInside = true;
        }, ['testing' => 2020]);

        $origin->onSuccess(function($task, $args) {
            $this->assertInstanceOf(CronTask::class, $task);
            $this->assertArrayHasKey('testing', $args);
            $this->assertEquals(2020, $args['testing']);
        });

        $dispatcher = new Dispatcher;

        $dispatcher($origin);
    }

    public function testCantDispatchButRunFailureCallbacks(): void
    {
        $origin = new CronTask(static function() {
            throw new \RuntimeException('Error');
        }, ['testing' => 2020]);

        $origin->onFailure(function($task, $args, $message) {
            $this->assertInstanceOf(CronTask::class, $task);
            $this->assertArrayHasKey('testing', $args);
            $this->assertEquals(2020, $args['testing']);
            $this->assertEquals('Error', $message);
        });

        $dispatcher = new Dispatcher;

        $dispatcher($origin);
    }
}