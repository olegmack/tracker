<?php

namespace Oro\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $container = self::$kernel->getContainer();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            $container->get('translator')->trans('oro.auth.sign_in_title'),
            $client->getResponse()->getContent()
        );
    }
}
