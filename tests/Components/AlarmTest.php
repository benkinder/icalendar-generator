<?php

namespace Spatie\Calendar\Tests\Components;

use DateTime;
use Spatie\Calendar\Components\Alarm;
use Spatie\Calendar\Duration;
use Spatie\Calendar\PropertyTypes\Parameter;
use Spatie\Calendar\PropertyTypes\TextPropertyType;
use Spatie\Calendar\Tests\TestCase;

class AlarmTest extends TestCase
{
    /** @test */
    public function it_can_create_an_alarm()
    {
        $payload = Alarm::new('It is time')->getPayload();

        $this->assertEquals('ALARM', $payload->getType());
        $this->assertEquals(2, count($payload->getProperties()));

        $this->assertPropertyEqualsInPayload('ACTION', 'DISPLAY', $payload);
        $this->assertPropertyEqualsInPayload('DESCRIPTION', 'It is time', $payload);
    }

    /** @test */
    public function it_can_trigger_before_an_event()
    {
        $duration = Duration::new()->minutes(5)->backInTime();

        $payload = Alarm::new()->triggerBeforeEvent($duration)->getPayload();

        $this->assertPropertyEqualsInPayload('TRIGGER', $duration->build(), $payload);
        $this->assertParameterEqualsInProperty('RELATED', 'START', $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_trigger_after_an_event()
    {
        $duration = Duration::new()->hours(2);

        $payload = Alarm::new()->triggerAfterEvent($duration)->getPayload();

        $this->assertPropertyEqualsInPayload('TRIGGER', $duration->build(), $payload);
        $this->assertParameterEqualsInProperty('RELATED', 'END', $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_trigger_at_a_specified_date_time()
    {
        $date = new DateTime('16 may 2019');

        $payload = Alarm::new()->triggerAt($date)->getPayload();

        $this->assertPropertyEqualsInPayload('TRIGGER', $date, $payload);
        $this->assertParameterEqualsInProperty('VALUE', 'DATE-TIME', $payload->getProperty('TRIGGER'));
    }

    /** @test */
    public function it_can_repeat_an_alarm_several_times()
    {
        $duration = Duration::new()->hours(2);

        $payload = Alarm::new()->repeat($duration, 2)->getPayload();

        $this->assertEquals($duration->build(), $payload->getProperty('DURATION')->getOriginalValue());
        $this->assertEquals(2, $payload->getProperty('REPEAT')->getOriginalValue());

        $this->assertPropertyEqualsInPayload('DURATION', $duration->build(), $payload);
        $this->assertPropertyEqualsInPayload('REPEAT', 2, $payload);
    }
}