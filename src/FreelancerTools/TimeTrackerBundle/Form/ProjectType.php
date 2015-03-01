<?php

namespace FreelancerTools\TimeTrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\User;
use FreelancerTools\CoreBundle\Entity\Customer;

class ProjectType extends AbstractType {

    protected $em;

    /**
     * @var User
     */
    protected $user;
    
    //protected $customer;

    public function __construct($em, User $user) {
        $this->em = $em;
        $this->user = $user;
        //$this->customer = $customer;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        //$transformer = new TagTransformer($this->em, $this->user);

        $builder
                ->add('name')
                ->add('alias')
                ->add('customer', null, array('required' => true))
                ->add('startedAt', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'with_seconds' => true,
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('stoppedAt', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'with_seconds' => true,
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('deadline', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'with_seconds' => true,
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('description')
                ->add('budgetPrice')
                ->add('fixedPrice')
                ->add('budgetTime')
                ->add('rate')
                ->add('active', null, array('required' => false))
        //->add($builder->create('tags', 'text')->addModelTransformer($transformer))
        ;
    }

    public function getName() {
        return 'ft_timetrackerbundle_projecttype';
    }

}
