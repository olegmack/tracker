<?php

namespace Oro\TestBundle\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * Default user name and password
     */
    const AUTH_USER = 'admin';
    const AUTH_PW = 'admin123';

    /**
     * @var Client
     */
    private static $clientInstance;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server An array of server parameters
     * @param bool $force If this option - true, will reset client on each initClient call
     *
     * @return Client A Client instance
     */
    protected function initClient(array $options = array(), array $server = array(), $force = false)
    {
        if ($force) {
            $this->resetClient();
        }

        if (!self::$clientInstance) {
            /** @var Client $client */
            self::$clientInstance = $this->createClient($options, $server);
        } else {
            self::$clientInstance->setServerParameters($server);
        }

        $this->client = self::$clientInstance;
    }


    /**
     * Reset client and rollback transaction
     */
    protected function resetClient()
    {
        echo 'boom';
        if (self::$clientInstance) {
            $this->client = null;
            self::$clientInstance = null;
        }
    }

    /**
     * @return Client
     * @throws \BadMethodCallException
     */
    public static function getClientInstance()
    {
        if (!self::$clientInstance) {
            throw new \BadMethodCallException('Client instance is not initialized.');
        }

        return self::$clientInstance;
    }

    /**
     * Generate Basic  authorization header
     *
     * @param string $userName
     * @param string $userPassword
     *
     * @return array
     */
    public static function generateBasicAuthHeader(
        $userName = self::AUTH_USER,
        $userPassword = self::AUTH_PW
    )
    {
        return array(
            'PHP_AUTH_USER' => $userName,
            'PHP_AUTH_PW' => $userPassword
        );
    }

    protected function tearDown()
    {
        $refClass = new \ReflectionClass($this);
        foreach ($refClass->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    public static function tearDownAfterClass()
    {
        if (self::$clientInstance) {
            self::$clientInstance = null;
        }
    }

    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * @param string $name
     * @param array  $parameters
     * @param bool   $absolute
     */
    protected function getUrl($name, $parameters = array(), $absolute = false)
    {
        return self::getContainer()->get('router')->generate($name, $parameters, $absolute);
    }

    /**
     * Get an instance of the dependency injection container.
     *
     * @return ContainerInterface
     */
    protected static function getContainer()
    {
        return static::getClientInstance()->getContainer();
    }
}
