<?php

namespace Rumur\WordPress\Scheduling\Test;

use PHPUnit\Framework\TestCase;
use Rumur\WordPress\Scheduling\Intervals;

class IntervalsTest extends TestCase
{
    public function testCanAddIntervals(): void
    {
        ($intervals = new Intervals)->add($intervalName = 'for-testing', $intervalTime = 10 * MINUTE_IN_SECONDS);

        $this->assertArrayHasKey($intervalName, $intervals->all());

        $this->assertTrue($intervals->has($intervalName));

        $this->assertFalse($intervals->has('invalid-interval-name'));

        $this->assertSame($intervalTime, $intervals->get($intervalName)['interval']);
    }

    public function testCanConvertToWordPressArray(): void
    {
        ($intervals = new Intervals)->add($intervalName = 'for-testing', $intervalTime = 10 * MINUTE_IN_SECONDS);

        $this->assertArrayHasKey($intervalName, $asArray = $intervals->toWordPressArray());

        $this->assertArrayHasKey('interval', $asArray[$intervalName]);
        $this->assertArrayHasKey('display', $asArray[$intervalName]);

        $this->assertSame($intervalTime, $asArray[$intervalName]['interval']);
    }

    public function testCantOverwriteDefaultWordPressIntervals(): void
    {
        ($intervals = new Intervals)->add($intervalName = 'twicedaily', $intervalTime = 1111);

        $default = [
            $intervalName => [
                'interval' => $defaultIntervalTime = 2222,
                'display' => 'Twice Daily',
            ]
        ];

        $merged = $intervals->registrar($default);

        $this->assertArrayHasKey('interval', $merged[$intervalName]);
        $this->assertArrayHasKey('display', $merged[$intervalName]);

        $this->assertSame($defaultIntervalTime, $merged[$intervalName]['interval']);
    }
}