<?php

namespace FreelancerTools\TimeTrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\User;
use FreelancerTools\CoreBundle\Entity\Customer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class ActivityType extends AbstractType {

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
                    'data_class' => 'FreelancerTools\TimeTrackerBundle\Entity\Activity',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $user = $this->user;

        $builder
                ->add('description')
                ->add('rate')
                ->add('notes')
                ->add('archived', null, array('required' => false))
        ;                  
    }

    public function getName() {
        return 'ft_timetrackerbundle_activitytype';
    }

}
