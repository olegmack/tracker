<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Form\IssueType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueType
     */
    protected $type;

    protected function setUp()
    {
        parent::setUp();

        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();

        $user = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();

        $this->type = new IssueType($manager, $user, array());
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
        $this->assertEquals('oro_issuebundle_issue', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'summary' => 'text',
            'description' => 'textarea',
            'issueType' => 'entity',
            'issuePriority' => 'entity',
            'issueResolution' => 'entity',
            'issueStatus' => 'entity',
            'assignee' => 'entity',
            'parent' => 'entity'
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

        $issue = $this->getMockBuilder('Oro\IssueBundle\Entity\Issue')
            ->disableOriginalConstructor()->getMock();

        $issue->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('1'));

        $project = $this->getMockBuilder('Oro\ProjectBundle\Entity\project')
            ->disableOriginalConstructor()->getMock();

        $project->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('1'));

        $issue->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($project));

        $options['data'] = $issue;

        $this->type->buildForm($builder, $options);
    }
}
