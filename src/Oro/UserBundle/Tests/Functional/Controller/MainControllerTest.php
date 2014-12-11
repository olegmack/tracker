<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testDashboard()
    {
        $this->client->request('GET', $this->getUrl('dashboard'));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains(
            $this->getContainer()->get('translator')->trans('oro.dashboard.projects_activity'),
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            $this->getContainer()->get('translator')->trans('oro.dashboard.issues_title'),
            $this->client->getResponse()->getContent()
        );
    }
}
