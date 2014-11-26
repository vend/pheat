<?php

namespace Pheat;

use Pheat\Test\Test;

class ManagerTest extends Test
{
    const SUT = 'Pheat\Manager';

    public function testConstructorOptional()
    {
        $this->assertInstanceOf(self::SUT, new Manager());
        new Manager(null, [$this->getMockProvider()]);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testAddProviderTypeHinting()
    {
        $manager = $this->getManager();
        $manager->addProvider(new \stdClass());
    }

    public function testAddProviderNonInt()
    {
        $provider = $this->getMockProvider();

        $manager = $this->getManager();
        $manager->addProvider($provider, 'some string');

        $this->assertEquals([$provider], $this->getObjectAttribute($manager, 'providers'));
    }

    /**
     * And a hint of merge-down testing
     */
    public function testProviderOrdering()
    {
        $manager = $this->getManager();

        $first = $this->getMockProvider('first', ['1' => true, '2' => true, '3' => true, '4' => true]);
        $second = $this->getMockProvider('second', ['2' => true, '3' => true, '4' => true]);
        $third = $this->getMockProvider('third', ['3' => true, '4' => true]);
        $fourth = $this->getMockProvider('fourth', ['4' => true]);

        $manager->addProvider($first);
        $manager->addProvider($third);
        $manager->addProvider($second, 1);
        $manager->addProvider($fourth, -1);

        $resolved = $manager->resolveAll();
        $this->assertInternalType('array', $resolved);

        $this->assertEquals($first, $resolved['1']->getProvider());
        $this->assertEquals($second, $resolved['2']->getProvider());
        $this->assertEquals($third, $resolved['3']->getProvider());
        $this->assertEquals($fourth, $resolved['4']->getProvider());
    }
}
