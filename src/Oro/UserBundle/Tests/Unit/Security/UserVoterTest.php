<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Security\UserVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserVoter
     */
    protected $voter;

    protected function setUp()
    {
        $this->voter = new UserVoter();
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
            'positive' => ['Oro\UserBundle\Entity\User', true],
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
            UserVoter::VIEW  => [UserVoter::VIEW, true],
            UserVoter::EDIT  => [UserVoter::EDIT, true],
            'ASSIGN' => ['ASSIGN', false],
            UserVoter::EDIT_ROLE => [UserVoter::EDIT_ROLE, true],
            UserVoter::VIEW_LIST => [UserVoter::VIEW_LIST, true],
            'DELETE' => ['DELETE', false],
        ];
    }


    /**
     * @param int $expected
     * @param string $objectClass
     * @param array $attributes
     * @param string $role
     * @param string $currentUsername
     * @param string $objectUsername
     * @dataProvider voteDataProvider
     */
    public function testVote($expected, $objectClass, array $attributes = [],
         $role = 'ROLE_USER', $currentUsername = '', $objectUsername = '')
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
            ->will($this->returnValue($currentUsername));

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($currentUser));

        $object = $this->getMockBuilder($objectClass)
            ->disableOriginalConstructor()->getMock();

        $object->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($objectUsername));

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
                'objectClass' => 'Oro\UserBundle\Entity\Role',
            ],
            'wrong attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => ['DELETE'],
            ],
            'no attributes' => [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [],
            ],
            'view access granted' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::VIEW]
            ],
            'view list access granted' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::VIEW_LIST],
                'role'  => 'ROLE_ADMIN',
            ],
            'view list access denied' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::VIEW_LIST],
                'role'  => 'ROLE_MANAGER',
            ],
            'edit role access granted' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::EDIT_ROLE],
                'role'  => 'ROLE_ADMIN',
            ],
            'edit role access denied' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::EDIT_ROLE],
            ],
            'edit access user denied' => [
                'expected' => VoterInterface::ACCESS_DENIED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::EDIT],
                'role'  => 'ROLE_USER',
                'currentUsername' => 'test',
                'objectUsername'  => 'test1'
            ],
            'edit access user granted' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::EDIT],
                'role'  => 'ROLE_USER',
                'currentUsername' => 'test',
                'objectUsername'  => 'test'
            ],
            'edit access admin granted' => [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'objectClass' => 'Oro\UserBundle\Entity\User',
                'attributes' => [UserVoter::EDIT],
                'role'  => 'ROLE_ADMIN',
                'currentUsername' => 'admin',
                'objectUsername'  => 'test'
            ]
        ];
    }
}