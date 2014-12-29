<?php

namespace Oro\ProjectBundle\Tests\Unit;

use Oro\ProjectBundle\Entity\Project;
use Oro\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Project();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');

        return array(
            'code'    => array('code', 'TST', 'TST'),
            'name'    => array('name', 'Test Project', 'Test Project'),
            'summary' => array('summary', 'Test Project Summary', 'Test Project Summary'),
            'users'   => array('users', new ArrayCollection(array($user)), new ArrayCollection(array($user)))
        );
    }

    public function testUserAddRemove()
    {
        $obj = new Project();
        $user = new User();
        $obj->addUser($user);
        $this->assertTrue($obj->hasUser($user));

        $obj->removeUser($user);
        $this->assertFalse($obj->hasUser($user));
    }
}
