<?php

namespace Oro\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    'label' => 'oro.project.fields.name_label',
                    'attr' => array('class'=>'form-control')
                )
            )
            ->add(
                'code',
                'text',
                array(
                    'label' => 'oro.project.fields.code_label',
                    'attr'=>array('class'=>'form-control')
                )
            )
            ->add(
                'summary',
                'textarea',
                array(
                    'label' => 'oro.project.fields.summary_label',
                    'attr'=>array('class'=>'form-control'),
                    'required' => false
                )
            )
            ->add(
                'users',
                'entity',
                array(
                    'label' => 'oro.project.fields.users_label',
                    'property_path' => 'users',
                    'class'         => 'OroUserBundle:User',
                    'property'      => 'name',
                    'multiple'      => true,
                    'attr' => array('class'=>'form-control')
                )
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_projectbundle_project';
    }
}
