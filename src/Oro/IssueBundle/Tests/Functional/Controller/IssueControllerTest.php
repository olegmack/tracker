<?php

namespace Oro\IssueBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testCompleteScenario()
    {
        // Create a new entry in the database
        $crawler = $this->client->request('GET', $this->getUrl('issue_new'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");

        $this->assertContains(
            'Create New Issue',
            $this->client->getResponse()->getContent()
        );

        //filling the form
        $form = $crawler->selectButton('Create Issue')->form();
        $form->setValues(array(
            'oro_issuebundle_issue[summary]'     => 'Test Issue',
            'oro_issuebundle_issue[description]' => 'Description for Test Issue',
        ));

        $form['oro_issuebundle_issue[project]']->select(
            $crawler->filter('#oro_issuebundle_issue_project option:contains("Business Application Platform")')->attr('value')
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'Issue has been successfully created',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Test Issue',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Description for Test Issue',
            $this->client->getResponse()->getContent()
        );

        //edit
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form();
        $form->setValues(array(
            'oro_issuebundle_issue[summary]'     => 'Test Issue - Changed',
            'oro_issuebundle_issue[description]' => 'Description for Test Issue - Changed',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'Test Issue - Changed',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Description for Test Issue - Changed',
            $this->client->getResponse()->getContent()
        );

        //add comment
        $form = $crawler->selectButton('Add')->form(array(
            'oro_issuebundle_comment[body]' => 'Test Comment'
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        $this->assertContains(
            'Test Comment',
            $this->client->getResponse()->getContent()
        );

        //check in project list
        $crawler = $this->client->click($crawler->selectLink(
            'Business Application Platform'
        )->link());

        $this->assertContains(
            'Test Issue - Changed',
            $this->client->getResponse()->getContent()
        );

        //delete
        $this->removeTestIssue('Test Issue - Changed');
    }

    /**
     * Remove user with defined e-mail
     * @param $summary
     */
    protected function removeTestIssue($summary)
    {
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $testIssue = $em->getRepository('OroIssueBundle:Issue')->findOneBySummary($summary);
        $em->remove($testIssue);
        $em->flush();
    }
}
