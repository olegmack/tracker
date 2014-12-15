<?php

namespace Oro\IssueBundle\Tests\Security;

use Oro\IssueBundle\Security\CommentVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommnetVoter
     */
    protected $voter;

    protected function setUp()
    {
        $this->voter = new CommentVoter();
    }

    /**
     * @param string $class
     * @param bool $expected
     * @dataProvider supportsClassDataProvider
     */
    public function testSupportsClass($class, $expected)
    {
        $this->assertEquals($expected, $this->voter->supportsClass($class));
    }

    /**
     * @return array
     */
    public function supportsClassDataProvider()
    {
        return [
            'positive' => ['Oro\IssueBundle\Entity\Comment', true],
            'nagative' => ['OtherClass', false],
        ];
    }

    /**
     * @param string $attribute
     * @param bool $expected
     * @dataProvider supportsAttributeDataProvider
     */
    public function testSupportsAttribute($attribute, $expected)
    {
        $this->assertEquals($expected, $this->voter->supportsAttribute($attribute));
    }

    /**
     * @return array
     */
    public function supportsAttributeDataProvider()
    {
        return [
            CommentVoter::CREATE => [CommentVoter::CREATE, true],
            CommentVoter::MODIFY => [CommentVoter::MODIFY, true],
            'NONEXISTENT' => ['NONEXISTENT', false],
        ];
    }


    /**
     * @param int $expected
     * @param string $objectClass
     * @param array $attributes
     * @param string $role
     * @param bool $isMember
     * @param string $currentUsername
     * @param string $objectUsername
     * @dataProvider voteDataProvider
     */
    public function testVote(
        $expected,
        $objectClass,
        array $attributes = [],
        $role = 'ROLE_USER',
        $isMember = false,
        $currentUsername = '',
        $objectUsername = ''
    ) {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $currentUser = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $currentUser->expects($this->any())
            ->method('getRole')
            ->will($this->returnCallback(function ($expectedRole) use ($role) {
                return $expectedRole == $role;
            }));

        $currentUser->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($currentUsername));

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($currentUser));

        $project = $this->getMockBuilder('Oro\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()->getMock();

        $project->expects($this->any())
            ->method('isMember')
            ->will($this->returnValue($isMember));

        $issue = $this->getMockBuilder('Oro\IssueBundle\Entity\Issue')
            ->disableOriginalConstructor()->getMock();

        $issue->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($project));

        $author = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $author->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($objectUsername));

        $object = $this->getMockBuilder($objectClass)
            ->disableOriginalConstructor()->getMock();

        $object->expects($this->any())
            ->method('getIssue')
            ->will($this->returnValue($issue));

        $object->expects($this->any())
            ->method('getAuthor')
            ->will($this->returnValue($author));

        $this->assertEquals($expected, $this->voter->vote($token, $object, $attributes));
    }

    /**
     * Vote data provider
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function voteDataProvider()
    {
        return [
            'wrong object' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\UserBundle\Entity\User',
            ],
            'wrong attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => ['NONEXISTENT'],
            ],
            'no attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [],
            ],
            'create access granted - user is member' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::CREATE],
                'role' => 'ROLE_USER',
                'isMember' => true
            ],
            'create access denied - not member' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::CREATE],
                'role' => 'ROLE_USER',
                'isMember' => false
            ],
            'create access granted - user admin' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::CREATE],
                'role' => 'ROLE_ADMIN'
            ],
            'modify access granted - admin' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::MODIFY],
                'role' => 'ROLE_ADMIN',
            ],
            'modify access granted - author' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::MODIFY],
                'role' => 'ROLE_USER',
                'isMember' => true,
                'currentUsername' => 'test',
                'objectUsername'  => 'test'
            ],
            'modify access denied - not author' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\IssueBundle\Entity\Comment',
                'attributes' => [CommentVoter::MODIFY],
                'role' => 'ROLE_USER',
                'isMember' => true,
                'currentUsername' => 'test',
                'objectUsername'  => 'test1'
            ]
        ];
    }
}
