<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Issue();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $now = new \DateTime('now');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $project = $this->getMock('Oro\Bundle\ProjectBundle\Entity\Project');
        $issueType = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueType');
        $issuePriority = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssuePriority');
        $issueResolution = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueResolution');
        $issueStatus = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueStatus');

        return array(
            'summary' => array('summary', 'Test Issue Summary', 'Test Issue Summary'),
            'description' => array('description', 'Test Issue Description', 'Test Issue Description'),
            'createdAt'  => array('createdAt', $now, $now),
            'updatedAt'  => array('updatedAt', $now, $now),
            'assignee'  => array('assignee', $user, $user),
            'reporter'  => array('reporter', $user, $user),
            'issueType'  => array('issueType', $issueType, $issueType),
            'issuePriority'  => array('issuePriority', $issuePriority, $issuePriority),
            'issueResolution'  => array('issueResolution', $issueResolution, $issueResolution),
            'issueStatus'  => array('issueStatus', $issueStatus, $issueStatus),
            'project'  => array('project', $project, $project)
        );
    }
}