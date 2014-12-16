<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Form\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class UserTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserType
     */
    protected $type;

    protected function setUp()
    {
        parent::setUp();

        $securityInterface = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test'));

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $securityInterface->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));

        $this->type = new UserType($securityInterface);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->setDefaultOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('oro_userbundle_user', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'email' => 'email',
            'username' => 'text',
            'fullname' => 'text',
            'file' => 'file',
            'plainPassword' => 'password'
        );

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $counter = 0;
        foreach ($expectedFields as $fieldName => $formType) {
            $builder->expects($this->at($counter))
                ->method('add')
                ->with($fieldName, $formType)
                ->will($this->returnSelf());
            $counter++;
        }

        $user = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test'));

        $options['data'] = $user;

        $this->type->buildForm($builder, $options);
    }
}
