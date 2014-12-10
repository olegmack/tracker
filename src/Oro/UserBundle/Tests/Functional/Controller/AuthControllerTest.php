<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient();
    }

    public function testLogin()
    {
        $this->client->request('GET', $this->getUrl('login'));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains(
            $this->getContainer()->get('translator')->trans('oro.auth.sign_in_title'),
            $this->client->getResponse()->getContent()
        );
    }
}
