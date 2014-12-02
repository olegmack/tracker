<?php

namespace Oro\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'project',
                'entity',
                array(
                    'property_path' => 'project',
                    'class'         => 'OroProjectBundle:Project',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add('summary', 'text', array('attr'=>array('class'=>'form-control')))
            ->add('description', 'textarea', array('attr'=>array('class'=>'form-control')))
            ->add(
                'issueType',
                'entity',
                array(
                    'property_path' => 'issueType',
                    'class'         => 'OroIssueBundle:IssueType',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'issuePriority',
                'entity',
                array(
                    'property_path' => 'issuePriority',
                    'class'         => 'OroIssueBundle:IssuePriority',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'issueResolution',
                'entity',
                array(
                    'property_path' => 'issueResolution',
                    'class'         => 'OroIssueBundle:IssueResolution',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'issueStatus',
                'entity',
                array(
                    'property_path' => 'issueStatus',
                    'class'         => 'OroIssueBundle:IssueStatus',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'assignee',
                'entity',
                array(
                    'property_path' => 'assignee',
                    'class'         => 'OroUserBundle:User',
                    'property'      => 'name',
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'collaborators',
                'entity',
                array(
                    'property_path' => 'collaborators',
                    'class'         => 'OroUserBundle:User',
                    'property'      => 'name',
                    'required'      => false,
                    'multiple'      => true,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'parent',
                'entity',
                array(
                    'property_path' => 'parent',
                    'class'         => 'OroIssueBundle:Issue',
                    'empty_value'   => '--- Please choose a parent Issue ---',
                    'required'      => false,
                    'multiple'      => false,
                    'attr' => array('class'=>'form-control')
                )
            )
            ->remove('reporter');
        ;
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
