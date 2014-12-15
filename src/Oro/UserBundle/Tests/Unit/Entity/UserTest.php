<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new User();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $role = $this->getMock('Oro\Bundle\UserBundle\Entity\Role');
        $project = $this->getMock('Oro\Bundle\ProjectBundle\Entity\Project');

        return array(
            'email'    => array('email', 'testemail@oro.com', 'testemail@oro.com'),
            'username' => array('username', 'testunit', 'testunit'),
            'fullname' => array('fullname', 'John Doe', 'John Doe'),
            'avatar'   => array('avatar', 'filepath', 'filepath'),
            'password' => array('password', 'test', 'test'),
            'roles'    => array('roles', $role, array($role)),
            'project'  => array('projects', $project, $project)
        );
    }
}
