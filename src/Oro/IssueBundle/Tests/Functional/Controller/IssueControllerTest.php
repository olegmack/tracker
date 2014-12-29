<?php

namespace Oro\IssueBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class IssueControllerTest extends WebTestCase
{
    const PROJECT_NAME = 'BAP';
    const ISSUE_NAME = 'Test Issue';
    const ISSUE_DESCRIPTION = 'Description for Test Issue';
    const ISSUE_NAME_CHANGED = 'Test Issue - Changed';
    const ISSUE_DESCRIPTION_CHANGED = 'Description for Test Issue - Changed';
    const COMMENT = 'Test Comment';

    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    /**
     * @return int|null
     */
    public function testCreate()
    {
        //open project list
        $crawler = $this->client->request('GET', $this->getUrl('project'));
        $crawler = $this->client->click($crawler->selectLink(self::PROJECT_NAME)->link());
        $crawler = $this->client->click(
            $crawler->selectLink($this->getTrans('oro.project.create_issue_label'))->link()
        );

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for new Issue page"
        );

        $this->assertContains(
            $this->getTrans('oro.issue.new_issue_header'),
            $this->client->getResponse()->getContent()
        );

        //filling the form
        $form = $crawler->selectButton($this->getTrans('oro.issue.create_issue_button'))->form();
        $form->setValues(array(
            'oro_issuebundle_issue[summary]'     => self::ISSUE_NAME,
            'oro_issuebundle_issue[description]' => self::ISSUE_DESCRIPTION,
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            $this->getTrans('oro.issue.messages.issue_created'),
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            self::ISSUE_NAME,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            self::ISSUE_DESCRIPTION,
            $this->client->getResponse()->getContent()
        );

        $url = $this->client->getHistory()->current()->getUri();
        $id = $this->getIdFromUrl($url);

        $this->assertNotNull($id);

        return $id;
    }

    /**
     * @param int $id
     * @depends testCreate
     * @return int|null
     */
    public function testUpdate($id)
    {
        $crawler = $this->client->request('GET', $this->getUrl('issue_update', array('id' => $id)));

        $form = $crawler->selectButton($this->getTrans('oro.issue.update_button'))->form();

        $form->setValues(array(
            'oro_issuebundle_issue[summary]'     => self::ISSUE_NAME_CHANGED,
            'oro_issuebundle_issue[description]' => self::ISSUE_DESCRIPTION_CHANGED,
            'oro_issuebundle_issue[issueStatus]' => 'Resolved',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            self::ISSUE_NAME_CHANGED,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            self::ISSUE_DESCRIPTION_CHANGED,
            $this->client->getResponse()->getContent()
        );

        return $id;
    }

    /**
     * @param int $id
     * @depends testUpdate
     */
    public function testAddComment($id)
    {
        $crawler = $this->client->request('GET', $this->getUrl('issue_show', array('id' => $id)));
        $form = $crawler->selectButton($this->getTrans('oro.comment.add_button'))->form(
            array(
                'oro_issuebundle_comment[body]' => self::COMMENT
            )
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertContains(
            self::COMMENT,
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * @param int $id
     * @depends testUpdate
     */
    public function testViewInProject($id)
    {
        //open project list
        $crawler = $this->client->request('GET', $this->getUrl('project'));
        $this->client->click($crawler->selectLink(self::PROJECT_NAME)->link());

        $this->assertContains(
            self::ISSUE_NAME_CHANGED,
            $this->client->getResponse()->getContent()
        );

        //delete
        $this->removeTestIssue($id);
    }

    /**
     * Remove test issue
     *
     * @param int $id
     */
    protected function removeTestIssue($id)
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();
        $testIssue = $em->getRepository('OroIssueBundle:Issue')->find($id);
        $em->remove($testIssue);
        $em->flush();
    }
}
