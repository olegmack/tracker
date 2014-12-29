<?php

namespace Oro\IssueBundle\Tests\Unit;

use Symfony\Component\Form\Test\FormIntegrationTestCase;

use Oro\IssueBundle\Form\CommentType;
use Oro\IssueBundle\Entity\Comment;

class CommentTypeTest extends FormIntegrationTestCase
{
    /**
     * @var CommentType
     */
    protected $type;

    protected function setUp()
    {
        parent::setUp();
        $this->type = new CommentType();
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
        $this->assertEquals('oro_issuebundle_comment', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'body' => 'textarea'
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

    /**
     * Test form submit
     */
    public function testSubmitData()
    {
        $form = $this->factory->create($this->type);

        $formData = [
            'body' => 'Test Comment body'
        ];

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        /** @var Comment $result */
        $result = $form->getData();
        $this->assertEquals($formData['body'], $result->getBody());
    }
}
