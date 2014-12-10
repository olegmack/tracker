<?php

namespace Oro\ProjectBundle\Tests\Unit;

use Oro\ProjectBundle\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
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
        $issue = $this->getMock('Oro\Bundle\IssueBundle\Entity\Issue');

        return array(
            'code'    => array('code', 'TST', 'TST'),
            'name'    => array('name', 'Test Project', 'Test Project'),
            'summary' => array('summary', 'Test Project Summary', 'Test Project Summary'),
            'users'   => array('users', new ArrayCollection(array($user)), new ArrayCollection(array($user)))
        );
    }
}