<?php

namespace Oro\ProjectBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    const PROJECT_NAME = 'Test Project';
    const PROJECT_CODE = 'TEST';
    const PROJECT_SUMMARY = 'Test Project Description';
    const PROJECT_NAME_CHANGED = 'Test Project - Changed';
    const PROJECT_CODE_CHANGED = 'TEST-CHG';
    const PROJECT_SUMMARY_CHANGED = 'Test Project Description - Changed';

    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('project'));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /project/"
        );

        $this->assertContains(
            $this->getTrans('oro.project.projects_title'),
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('project_create'));

        // Fill in the form and submit it
        $form = $crawler->selectButton($this->getTrans('oro.project.create_label'))->form();
        $form->setValues(array(
            'oro_projectbundle_project[name]'     => self::PROJECT_NAME,
            'oro_projectbundle_project[code]'     => self::PROJECT_CODE,
            'oro_projectbundle_project[summary]'  => self::PROJECT_SUMMARY
        ));

        $form['oro_projectbundle_project[users]']->select(
            $crawler->filter('#oro_projectbundle_project_users option:contains("manager")')->attr('value')
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $result = $this->client->getResponse()->getContent();
        $this->assertContains(
            $this->getTrans('oro.project.messages.project_added'),
            $result
        );

        $this->assertContains(
            $this->getTrans('oro.project.show_title', array('name' => self::PROJECT_NAME)),
            $result
        );

        $url = $this->client->getHistory()->current()->getUri();
        $id = $this->getIdFromUrl($url);

        $this->assertNotNull($id);

        return $id;
    }

    /**
     * @param int $id
     * @depends testCreate
     * @return int
     */
    public function testUpdate($id)
    {
        $crawler = $this->client->request('GET', $this->getUrl('project_update', array('id' => $id)));

        $form = $crawler->selectButton($this->getTrans('oro.project.update_label'))->form();
        $form->setValues(array(
            'oro_projectbundle_project[name]'     => self::PROJECT_NAME_CHANGED,
            'oro_projectbundle_project[code]'     => self::PROJECT_CODE_CHANGED,
            'oro_projectbundle_project[summary]'  => self::PROJECT_SUMMARY_CHANGED
        ));

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertContains(
            self::PROJECT_NAME_CHANGED,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(self::PROJECT_SUMMARY_CHANGED, $this->client->getResponse()->getContent());

        //check in projects list
        $this->client->request('GET', $this->getUrl('project'));
        $this->assertContains(
            self::PROJECT_NAME_CHANGED,
            $this->client->getResponse()->getContent()
        );

        return $id;
    }

    /**
     * @param int $id
     * @depends testUpdate
     * @return int
     */
    public function testDelete($id)
    {
        $crawler = $this->client->request('GET', $this->getUrl('project_update', array('id' => $id)));
        $form = $crawler->selectButton($this->getTrans('oro.project.delete_label'))->form();
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertNotContains(self::PROJECT_NAME_CHANGED, $this->client->getResponse()->getContent());
        $this->assertNotContains(self::PROJECT_CODE_CHANGED, $this->client->getResponse()->getContent());
    }
}
