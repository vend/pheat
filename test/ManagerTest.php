<?php

namespace Pheat;

use Pheat\Feature\FeatureInterface;
use Pheat\Provider\ProviderInterface;
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

        $first  = $this->getMockProvider('first',  ['1' => Status::ACTIVE, '2' => Status::ACTIVE, '3' => Status::ACTIVE, '4' => Status::INACTIVE]);
        $second = $this->getMockProvider('second', ['2' => Status::ACTIVE, '3' => Status::ACTIVE, '4' => Status::ACTIVE]);
        $third  = $this->getMockProvider('third',  ['3' => Status::ACTIVE, '4' => Status::ACTIVE]);
        $fourth = $this->getMockProvider('fourth', ['4' => Status::ACTIVE]);

        $manager->addProvider($first);
        $manager->addProvider($third);
        $manager->addProvider($second, 1);
        $manager->addProvider($fourth, -1);

        $set = $manager->getFeatureSet();
        $this->assertInstanceOf('Pheat\Feature\Set', $set);

        $this->assertEquals($first, $set->getFeature('1')->getProvider());
        $this->assertEquals($second, $set->getFeature('2')->getProvider());
        $this->assertEquals($third, $set->getFeature('3')->getProvider());
        $this->assertEquals($fourth, $set->getFeature('4')->getProvider());

        return $manager;
    }

    /**
     * @depends testProviderOrdering
     */
    public function testResolveBoolean(Manager $manager)
    {
        $this->assertTrue($manager->resolve('1'));
        $this->assertTrue($manager->resolve('2'));
        $this->assertTrue($manager->resolve('3'));
        $this->assertFalse($manager->resolve('4'));
        $this->assertNull($manager->resolve('unknown_feature'));
    }

    public function testBadProviderException()
    {
        $bad = $this->getMockBuilder('Pheat\Provider\NullProvider')
                             ->setMethods(['getFeatures'])
                             ->getMock();

        $bad->expects($this->atLeastOnce())
                     ->method('getFeatures')
                     ->with($this->isInstanceOf('Pheat\ContextInterface'))
                     ->will($this->throwException(new \Exception('The provider could not retrieve feature information')));

        $this->assertBadProviderHandled($bad, 'Provider that throws exception handled correctly');
    }

    public function testBadProviderType()
    {
        $bad = $this->getMockBuilder('Pheat\Provider\NullProvider')
                    ->setMethods(['getFeatures'])
                    ->getMock();

        $bad->expects($this->atLeastOnce())
            ->method('getFeatures')
            ->with($this->isInstanceOf('Pheat\ContextInterface'))
            ->will($this->returnValue('a string? for an array of features? yeah, that will cause problems'));

        $this->assertBadProviderHandled($bad, 'Provider that does not return array for features handled correctly');
    }

    /**
     * @depends testProviderOrdering
     * @expectedException \Pheat\Exception\LockedException
     */
    public function testLocking(Manager $manager)
    {
        $manager->addProvider($this->getMockProvider());
    }

    public function testGetContext()
    {
        $context = new Context();
        $manager = $this->getManager($context);
        $this->assertEquals($context, $manager->getContext());
    }

    public function testGetProviders()
    {
        $manager = $this->getManager(null, [
            $this->getMockProvider('a'),
            $this->getMockProvider('b')
        ]);

        $providers = $manager->getProviders();
        $provider  = $manager->getProvider('a');

        $this->assertInternalType('array', $providers);
        $this->assertInstanceOf(ProviderInterface::class, $provider);
    }

    public function testGetFeature()
    {
        $manager = $this->getManager(null, [
            $this->getMockProvider('baz', ['foo' => true, 'bar' => false])
        ]);

        $foo = $manager->getFeature('foo');
        $bar = $manager->getFeature('bar');

        $this->assertInstanceOf(FeatureInterface::class, $foo);
        $this->assertInstanceOf(FeatureInterface::class, $bar);
    }

    protected function assertBadProviderHandled(ProviderInterface $bad, $message = '')
    {
        $manager = $this->getManager();

        $manager->addProvider($this->getMockProvider('good', ['working' => true]));
        $manager->addProvider($bad);
        $manager->addProvider($this->getMockProvider('good2', ['continues' => true]));

        $this->assertTrue($manager->resolve('working'), $message);
        $this->assertTrue($manager->resolve('continues'), $message);
    }
}
