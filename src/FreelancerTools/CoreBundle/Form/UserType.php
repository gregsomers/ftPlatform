<?php

namespace FreelancerTools\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username')                
                ->add('email')                
                ->add('enabled', null, array(
                    'required' => false,
                ))
                /*
                ->add('roles', "choice", array(
                    'choices' => array(
                        'ROLE_USER' => 'ROLE_USER',
                        'ROLE_ORDER_ADMIN' => 'ROLE_ORDER_ADMIN',
                        'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                        //'ROLE_ALLOWED_TO_SWITCH' => 'ROLE_ALLOWED_TO_SWITCH'
                    ),
                    'multiple' => true,
                    'expanded' => false
                        )
                )*/
                ->add('firstName')
                ->add('lastName')
                ->add('company')
                ->add('address')              
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FreelancerTools\CoreBundle\Entity\User'
        ));
    }

    public function getName() {
        return 'ft_user_profile';
    }

}
