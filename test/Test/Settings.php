<?php

namespace Pheat\Test;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Resque\ClientInterface;
use RuntimeException;

class Settings implements LoggerAwareInterface
{
    const ENV_PREFIX = 'PHEAT_';

    protected $clientType;
    protected $host;
    protected $bind;
    protected $port;
    protected $db;
    protected $prefix;
    protected $buildDir;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->buildDir = __DIR__ . '/../../build';
    }

    public function fromEnvironment()
    {
        $env = array(
            'client_type' => 'clientType',
            'host'        => 'host',
            'port'        => 'port',
            'bind'        => 'bind',
            'build_dir'   => 'buildDir',
            'run'         => 'run',
            'db'          => 'db',
            'prefix'      => 'prefix'
        );

        foreach ($env as $var => $setting) {
            $name = self::ENV_PREFIX . strtoupper($var);

            if (isset($_SERVER[$name])) {
                $this->$setting = $_SERVER[$name];
            }
        }
    }

    public function setBuildDir($dir)
    {
        $this->buildDir = $dir;
    }

    public function checkBuildDir()
    {
        if (!is_dir($this->buildDir)) {
            mkdir($this->buildDir);
        }

        if (!is_dir($this->buildDir)) {
            throw new RuntimeException('Could not create build dir: ' . $this->buildDir);
        }
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Dumps configuration to a file
     *
     * @return void
     */
    public function dumpConfig()
    {
        $file = $this->buildDir . \DIRECTORY_SEPARATOR . 'settings.json';
        $config = json_encode(get_object_vars($this), JSON_PRETTY_PRINT);

        $this->logger->info('Dumping test config {config} to {file}', array('file' => $file, 'config' => $config));
        file_put_contents($file, $config);
    }
}
