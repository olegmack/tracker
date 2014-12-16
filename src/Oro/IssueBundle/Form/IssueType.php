<?php

namespace Oro\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oro\IssueBundle\Entity\IssueRepository;
use Doctrine\ORM\EntityRepository;
use Oro\UserBundle\Entity\User;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $issue = $options['data'];
        $issueId = $issue->getId();
        $projectId = $issue->getProject()->getId();

        $builder
            ->add(
                'summary',
                'text',
                array(
                    'label' => 'oro.issue.fields.summary_label',
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'label' => 'oro.issue.fields.description_label',
                    'attr'=>array('class'=>'form-control')
                )
            )
            ->add(
                'issueType',
                'entity',
                array(
                    'label' => 'oro.issue.fields.issue_type_label',
                    'property_path' => 'issueType',
                    'class'         => 'OroIssueBundle:IssueType',
                    'property'      => 'name',
                    'multiple'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                    },
                    'attr'          => array('class'=>'form-control')
                )
            )
            ->add(
                'issuePriority',
                'entity',
                array(
                    'label' => 'oro.issue.fields.issue_priority_label',
                    'property_path' => 'issuePriority',
                    'class'         => 'OroIssueBundle:IssuePriority',
                    'property'      => 'name',
                    'multiple'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                    },
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'issueResolution',
                'entity',
                array(
                    'label' => 'oro.issue.fields.issue_resolution_label',
                    'property_path' => 'issueResolution',
                    'class'         => 'OroIssueBundle:IssueResolution',
                    'property'      => 'name',
                    'multiple'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                    },
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'issueStatus',
                'entity',
                array(
                    'label' => 'oro.issue.fields.issue_status_label',
                    'property_path' => 'issueStatus',
                    'class'         => 'OroIssueBundle:IssueStatus',
                    'property'      => 'name',
                    'multiple'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                    },
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'assignee',
                'entity',
                array(
                    'label' => 'oro.issue.fields.assignee_label',
                    'property_path' => 'assignee',
                    'class'         => 'OroUserBundle:User',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'parent',
                'entity',
                array(
                    'label' => 'oro.issue.fields.parent_label',
                    'property_path' => 'parent',
                    'class'         => 'OroIssueBundle:Issue',
                    'empty_value'   => '--- Please choose a parent Issue ---',
                    'required'      => false,
                    'query_builder' => function (IssueRepository $er) use ($projectId, $issueId){
                        return $er->getIssuesByProjectId($projectId, $issueId);
                    },
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->remove('reporter');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\IssueBundle\Entity\Issue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_issuebundle_issue';
    }
}
