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

        $this->type = new UserType(false);
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
            'plainPassword' => 'password',
            'roles' => 'entity'
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

        $this->type->buildForm($builder, array());
    }
}