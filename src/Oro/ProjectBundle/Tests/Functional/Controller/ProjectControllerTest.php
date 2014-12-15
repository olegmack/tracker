<?php

namespace Oro\ProjectBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testCompleteScenario()
    {
        $container = self::$kernel->getContainer();

        // Create a new entry in the database
        $crawler = $this->client->request('GET', $this->getUrl('project'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");

        $this->assertContains(
            "Projects",
            $this->client->getResponse()->getContent()
        );

        /** @var Crawler $crawler */
        $crawler = $this->client->click($crawler->selectLink(
            $container->get('translator')->trans('oro.project.create_label')
        )->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton(
            $container->get('translator')->trans('oro.project.create_label')
        )->form();
        $projectName = 'Test Project';
        $form->setValues(array(
            'oro_projectbundle_project[name]'     => $projectName,
            'oro_projectbundle_project[code]'     => 'TEST',
            'oro_projectbundle_project[summary]'  => 'Test Project Description'
        ));

        $form['oro_projectbundle_project[users]']->select(
            $crawler->filter('#oro_projectbundle_project_users option:contains("manager")')->attr('value')
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'Project has been successfully created',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            $container->get('translator')->trans('oro.project.show_title', array('name' => $projectName)),
            $this->client->getResponse()->getContent()
        );

        //edit
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form();
        $projectName .= ' Changed';
        $form->setValues(array(
            'oro_projectbundle_project[name]'     => $projectName,
            'oro_projectbundle_project[code]'     => 'TEST-CHG',
            'oro_projectbundle_project[summary]'  => 'Test Project Description - Changed'
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertContains($projectName, $this->client->getResponse()->getContent());

        $this->assertContains('Test Project Description - Changed', $this->client->getResponse()->getContent());

        //check in projects list
        $crawler = $this->client->click($crawler->selectLink(
            '« Back to the Projects list'
        )->link());

        $this->assertContains(
            $container->get('translator')->trans('oro.project.projects_title'),
            $this->client->getResponse()->getContent()
        );

        $this->assertContains($projectName, $this->client->getResponse()->getContent());

        //delete
        $crawler = $this->client->click($crawler->selectLink('TEST-CHG')->link());
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertNotContains($projectName, $this->client->getResponse()->getContent());
        $this->assertNotContains('TEST-CHG', $this->client->getResponse()->getContent());
    }
}
