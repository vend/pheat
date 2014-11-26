<?php

namespace Pheat\Test;

use Pheat\Feature\FeatureInterface;
use Pheat\Manager;
use Pheat\Provider\ProviderInterface;
use Pheat\Status;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerAwareTrait;

/**
 * Abstract base test class
 */
abstract class Test extends PHPUnit_Framework_TestCase
{
    use LoggerAwareTrait;

    /**
     * @var Settings
     */
    protected static $settings = null;

    /**
     * @param Settings $settings
     */
    public static function setSettings(Settings $settings)
    {
        self::$settings = $settings;
    }

    public function setUp()
    {
        if (!self::$settings) {
            throw new \LogicException('You must supply the test case with a settings instance');
        }

        $this->logger = self::$settings->getLogger();
    }

    /**
     * @param string            $name
     * @param bool              $status
     * @param ProviderInterface $provider
     * @return FeatureInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockFeature($name = 'test', $status = Status::ACTIVE, ProviderInterface $provider = null)
    {
        if ($provider === null) {
            $provider = $this->getMockProvider('auto-null', []);
        }

        $mock = $this->getMockBuilder('Pheat\Feature\Feature')
                     ->setConstructorArgs([$name, $status, $provider])
                     ->setMethods(null)
                     ->getMock();

        return $mock;
    }

    /**
     * @param string $name
     * @param array<string,bool|null>  $features
     * @return ProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockProvider($name = 'test', $features = [])
    {
        $mock = $this->getMockBuilder('Pheat\Provider\ProviderInterface')
                     ->setMethods(['getName', 'getFeatures'])
                     ->getMockForAbstractClass();

        $mock->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($name));

        $collected = [];

        foreach ($features as $feature => $status) {
            $collected[] = $this->getMockFeature($feature, $status, $mock);
        }

        $mock->expects($this->any())
             ->method('getFeatures')
             ->will($this->returnValue($collected));

        return $mock;
    }

    /**
     * @return Manager
     */
    protected function getManager()
    {
        $manager = new Manager();
        $manager->setLogger($this->logger);

        return $manager;
    }
}
