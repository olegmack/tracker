<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\IssueActivity;

class IssueActivityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $property
     * @param $value
     * @param $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new IssueActivity();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $now = new \DateTime('now');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $issue = $this->getMock('Oro\Bundle\IssueBundle\Entity\Issue');

        return array(
            'details' => array('details', 'Test Activity Description', 'Test Activity Description'),
            'createdAt'  => array('createdAt', $now, $now),
            'type'  => array('type', IssueActivity::ACTIVITY_COMMENT, IssueActivity::ACTIVITY_COMMENT),
            'user'  => array('user', $user, $user),
            'issue'  => array('issue', $issue, $issue)
        );
    }
}
