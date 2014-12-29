<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Comment();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        $now = new \DateTime('now');
        $author = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $issue = $this->getMock('Oro\Bundle\IssueBundle\Entity\Issue');

        return array(
            'body' => array('body', 'Test Comment', 'Test Comment'),
            'createdAt'  => array('createdAt', $now, $now),
            'updatedAt'  => array('updatedAt', $now, $now),
            'author'  => array('author', $author, $author),
            'issue'  => array('issue', $issue, $issue)
        );
    }
}
