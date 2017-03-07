<?php

namespace Pheat\Test;

use Pheat\Context;
use Pheat\ContextInterface;
use Pheat\Exception\TestException;
use Pheat\Feature\FeatureInterface;
use Pheat\Manager;
use Pheat\Provider\ProviderInterface;
use Pheat\Status;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerAwareTrait;

/**
 * Abstract base test class
 */

abstract class Test extends TestCase
{
    use LoggerAwareTrait;

    const SUT = null;

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

    protected function getSut(/* ... */)
    {
        if (!static::SUT) {
            throw new TestException('Invalid system-under-test class');
        }

        $reflection = new \ReflectionClass(static::SUT);
        return $reflection->newInstanceArgs(func_get_args());
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
     * @param array  $features <string,bool|null>  $features
     * @param string $interface
     * @return ProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockProvider($name = 'test', $features = [], $interface = ProviderInterface::class)
    {
        $mock = $this->getMockBuilder($interface)
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
    protected function getManager(ContextInterface $context = null, array $providers = [])
    {
        $manager = $this->getMockBuilder('Pheat\Manager')
            ->setConstructorArgs([$context, $providers])
            ->setMethods(null)
            ->getMock();

        $manager->setLogger($this->logger);

        return $manager;
    }

    /**
     * @param array $contents
     * @return ContextInterface
     */
    protected function getMockContext(array $contents = [])
    {
        /**
         * @var Context $context
         */
        $context = $this->getMockBuilder(Context::class)
            ->setMethods(null)
            ->getMock();

        $context->setAll($contents);

        return $context;
    }
}
