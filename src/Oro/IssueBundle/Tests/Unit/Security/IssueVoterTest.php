<?php

namespace Oro\IssueBundle\Tests\Security;

use Oro\IssueBundle\Security\IssueVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IssueVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueVoter
     */
    protected $voter;

    protected function setUp()
    {
        $this->voter = new IssueVoter();
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
            'positive' => ['Oro\IssueBundle\Entity\Issue', true],
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
            IssueVoter::ACCESS  => [IssueVoter::ACCESS, true],
            'NONEXISTENT' => ['NONEXISTENT', false],
        ];
    }


    /**
     * @param int $expected
     * @param string $objectClass
     * @param array $attributes
     * @param string $role
     * @param bool $isMember
     * @dataProvider voteDataProvider
     */
    public function testVote(
        $expected,
        $objectClass,
        array $attributes = [],
        $role = 'ROLE_USER',
        $isMember = false
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
            ->will($this->returnValue('test'));

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($currentUser));

        $project = $this->getMockBuilder('Oro\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()->getMock();

        $project->expects($this->any())
            ->method('isMember')
            ->will($this->returnValue($isMember));

        $object = $this->getMockBuilder($objectClass)
            ->disableOriginalConstructor()->getMock();

        $object->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($project));

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
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => ['NONEXISTENT'],
            ],
            'no attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => [],
            ],
            'access granted - user is member' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => [IssueVoter::ACCESS],
                'role' => 'ROLE_USER',
                'isMember' => true
            ],
            'access denied - not member' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => [IssueVoter::ACCESS],
                'role' => 'ROLE_USER',
                'isMember' => false
            ],
            'access granted - manager' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => [IssueVoter::ACCESS],
                'role' => 'ROLE_MANAGER',
            ],
            'access granted - admin' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\IssueBundle\Entity\Issue',
                'attributes' => [IssueVoter::ACCESS],
                'role' => 'ROLE_ADMIN',
            ]
        ];
    }
}
