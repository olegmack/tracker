<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class UserControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testCompleteScenario()
    {
        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/user/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");

        $this->assertContains(
            "Users",
            $this->client->getResponse()->getContent()
        );

        /** @var Crawler $crawler */
        $crawler = $this->client->click($crawler->selectLink('+ Add User')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues(array(
            'oro_userbundle_user[email]'          => 'test@oro.com',
            'oro_userbundle_user[username]'       => 'test',
            'oro_userbundle_user[fullname]'       => 'John Doe',
            'oro_userbundle_user[plainPassword]'  => 'test123',
        ));

        $form['oro_userbundle_user[roles]']->select(
            $crawler->filter('#oro_userbundle_user_roles option:contains("Manager")')->attr('value')
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'User has been successfully added',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'test@oro.com',
            $this->client->getResponse()->getContent()
        );

        //edit
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form();
        $form->setValues(array(
            'oro_userbundle_user[email]'          => 'test@oro.com',
            'oro_userbundle_user[username]'       => 'test',
            'oro_userbundle_user[fullname]'       => 'John Doe - Changed'
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'John Doe - Changed',
            $this->client->getResponse()->getContent()
        );

        //check in users list
        $this->client->click($crawler->selectLink('« Back to the Users list')->link());
        $this->assertContains(
            '<h1>Users</h1>',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'John Doe - Changed',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'test@oro.com',
            $this->client->getResponse()->getContent()
        );

        $this->removeTestUser('test@oro.com');
    }

    /**
     * Remove user with defined e-mail
     * @param $email
     */
    protected function removeTestUser($email)
    {
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $testUser = $em->getRepository('OroUserBundle:User')->findOneByEmail($email);
        $em->remove($testUser);
        $em->flush();
    }
}
