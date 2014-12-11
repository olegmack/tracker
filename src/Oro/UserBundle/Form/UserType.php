<?php

namespace Oro\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @var bool
     */
    protected $isMyProfile;

    public function __construct($isMyProfile)
    {
        $this->isMyProfile = $isMyProfile;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'oro.user.email_label',
                'attr'=>array('class'=>'form-control'))
            )
            ->add('username', 'text', array(
                'label' => 'oro.user.username_label',
                'attr'=>array('class'=>'form-control'))
            )
            ->add('fullname', 'text', array(
                'label' => 'oro.user.fullname_label',
                'attr'=>array('class'=>'form-control'))
            )
            ->add('file', 'file', array(
                'label' => 'oro.user.avatar_label',
                'required' => false
            ))
            ->add(
                'plainPassword',
                'password',
                array(
                    'attr' => array('class'=>'form-control'),
                    'label' => 'oro.user.new_password_label',
                    'required' => false
                )
            );

        if (!$this->isMyProfile) {
            $builder->add(
                'roles',
                'entity',
                array(
                    'property_path' => 'rolesCollection',
                    'label' => 'oro.user.role_label',
                    'class' => 'OroUserBundle:Role',
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => array('class' => 'form-control')
                )
            );
        };
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
