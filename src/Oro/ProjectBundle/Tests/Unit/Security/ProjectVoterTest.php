<?php

namespace Oro\ProjectBundle\Tests\Security;

use Oro\ProjectBundle\Security\ProjectVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ProjectVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProjectVoter
     */
    protected $voter;

    protected function setUp()
    {
        $this->voter = new ProjectVoter();
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
            'positive' => ['Oro\ProjectBundle\Entity\Project', true],
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
            ProjectVoter::VIEW  => [ProjectVoter::VIEW, true],
            ProjectVoter::MODIFY  => [ProjectVoter::MODIFY, true],
            ProjectVoter::VIEW_LIST  => [ProjectVoter::VIEW_LIST, true],
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
    public function testVote($expected, $objectClass, array $attributes = [],
                             $role = 'ROLE_USER', $isMember = false)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $currentUser = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $currentUser->expects($this->any())
            ->method('getRole')
            ->will($this->returnCallback(function($expectedRole) use ($role) {
                return $expectedRole == $role;
            }));

        $currentUser->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test'));

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($currentUser));

        $object = $this->getMockBuilder($objectClass)
            ->disableOriginalConstructor()->getMock();

        $object->expects($this->any())
            ->method('isMember')
            ->will($this->returnValue($isMember));

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
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => ['NONEXISTENT'],
            ],
            'no attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [],
            ],
            'view access granted - user is member' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW],
                'role' => 'ROLE_USER',
                'isMember' => true
            ],
            'view access granted - user is manager' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW],
                'role' => 'ROLE_MANAGER',
                'isMember' => false
            ],
            'view access granted - user is admin' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW],
                'role' => 'ROLE_ADMIN',
                'isMember' => false
            ],
            'view access denied - not member' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW],
                'role' => 'ROLE_USER',
                'isMember' => false
            ],
            'view list access granted - admin' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW_LIST],
                'role'  => 'ROLE_ADMIN',
            ],
            'view list access denied - user' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW_LIST],
                'role'  => 'ROLE_USER',
                'isMember' => true
            ],
            'modify access granted - manager' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW_LIST],
                'role'  => 'ROLE_MANAGER',
            ],
            'modify access denied - user' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\ProjectBundle\Entity\Project',
                'attributes' => [ProjectVoter::VIEW_LIST],
                'role'  => 'ROLE_USER',
            ]
        ];
    }
}