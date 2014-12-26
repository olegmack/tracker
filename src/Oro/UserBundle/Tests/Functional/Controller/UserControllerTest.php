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

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('user'));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /user/"
        );

        $this->assertContains(
            $this->getTrans('oro.user.users_title'),
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('user_create'));

        // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues(array(
            'oro_userbundle_user[email]'          => 'test@oro.com',
            'oro_userbundle_user[username]'       => 'test_username',
            'oro_userbundle_user[fullname]'       => 'John Doe',
            'oro_userbundle_user[plainPassword]'  => 'test123',
        ));

        $form['oro_userbundle_user[roles]']->select(
            $crawler->filter('#oro_userbundle_user_roles option:contains("Manager")')->attr('value')
        );

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $result = $this->client->getResponse()->getContent();

        $this->assertContains(
            $this->getTrans('oro.user.messages.user_added'),
            $result
        );

        $this->assertContains(
            'test@oro.com',
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
     */
    public function testUpdate($id)
    {
        $crawler = $this->client->request('GET', $this->getUrl('user_update', array('id' => $id)));

        /** @var Crawler $crawler */
        $form = $crawler->selectButton($this->getTrans('oro.user.update'))->form();
        $form->setValues(array(
            'oro_userbundle_user[email]'          => 'test@oro.com',
            'oro_userbundle_user[username]'       => 'test',
            'oro_userbundle_user[fullname]'       => 'John Doe - Changed'
        ));

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertContains(
            'John Doe - Changed',
            $this->client->getResponse()->getContent()
        );

        //check in users list
        $this->client->request('GET', $this->getUrl('user'));
        $this->assertContains(
            '<h1>' . $this->getTrans('oro.user.users_title') .'</h1>',
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
     * Extract user id from url
     *
     * @param $url
     * @return int|null
     */
    protected function getIdFromUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $router = $this->getRouter()->match($path);
        return (isset($router['id'])) ? $router['id'] : null;
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
