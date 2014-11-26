<?php

namespace Pheat;

use Pheat\Test\Test;

class StatusTest extends Test
{
    public function testBooleanSemantics()
    {
        $this->assertTrue(Status::ACTIVE, 'Active interpreted as true');
        $this->assertFalse(Status::INACTIVE, 'Inactive interpreted as false');
        $this->assertFalse(Status::UNKNOWN === Status::INACTIVE, 'Inactive and unknown strictly distinct');
        $this->assertFalse(Status::UNKNOWN === Status::ACTIVE, 'Active and unknown strictly distinct');
    }

    public function testMessageAccess()
    {
        $this->assertEquals('unknown', Status::$messages[Status::UNKNOWN], 'Unknown message');
        $this->assertEquals('active', Status::$messages[Status::ACTIVE], 'Active message');
        $this->assertEquals('inactive', Status::$messages[Status::INACTIVE], 'Inactive message');
    }
}
