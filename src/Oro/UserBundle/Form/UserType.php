<?php

namespace Oro\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('attr'=>array('class'=>'form-control')))
            ->add('username', 'text', array('attr'=>array('class'=>'form-control')))
            ->add('fullname', 'text', array('attr'=>array('class'=>'form-control')))
            ->add('file', 'file', array(
                'label' => 'Avatar',
                'required' => false
            ))
            ->add(
                'plainPassword',
                'password',
                array(
                    'attr' => array('class'=>'form-control'),
                    'label' => 'New Password',
                    'required' => false
                )
            )
            ->add(
                'roles',
                'entity',
                array(
                    'property_path' => 'rolesCollection',
                    'class'         => 'OroUserBundle:Role',
                    'property'      => 'name',
                    'multiple'      => true,
                    'attr' => array('class'=>'form-control')
                )
            );
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_userbundle_user';
    }
}
