<?php

namespace FreelancerTools\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\User;

class CustomerType extends AbstractType {

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $user;

    public function __construct($em, User $user) {
        $this->em = $em;
        $this->user = $user;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\CoreBundle\Entity\Customer'
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {        

        $builder
                ->add('name')
                ->add('alias')
                ->add('address')
                ->add('contact', null, array('label' => "Contact Name"))
                ->add('phoneNumber')
                ->add('emailAddress')
                
                //->add($builder->create('tags', 'text')->addModelTransformer($transformer))
        ;
    }

    public function getName() {
        return 'ft_timetrackerbundle_customertype';
    }

}
