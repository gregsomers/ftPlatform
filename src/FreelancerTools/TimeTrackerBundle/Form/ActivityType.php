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
                //->add('rateReference')  // TODO: add constraints
                //->add('service')
                ->add('service', 'entity', array(
                    //'required' => true,
                    'label' => 'Service',
                    'class' => 'FreelancerToolsTimeTrackerBundle:Service',
                    'query_builder' => function(EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('t')
                                ->where('t.user = :user')
                                ->setParameter(':user', 1) //$user->getId()
                                ->orderBy('t.name', 'ASC')
                        ;
                    },
                    'property' => 'name',
                ))
                ->add('customer')
                ->add('archived', null, array('required' => false))
        ;

        $formModifier = function (FormInterface $form, Customer $customer = null) {
            $projects = null === $customer ? array() : $customer->getProjects();
            $form->add('project', null, array(
                'empty_value' => '',
                'choices' => $projects,
            ));
        };

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();

            $formModifier($event->getForm(), $data->getCustomer());
        }
        );

        $builder->get('customer')->addEventListener(
                FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            // It's important here to fetch $event->getForm()->getData(), as
            // $event->getData() will get you the client data (that is, the ID)
            $customer = $event->getForm()->getData();

            // since we've added the listener to the child, we'll have to pass on
            // the parent to the callback functions!
            $formModifier($event->getForm()->getParent(), $customer);
        }
        );
    }

    public function getName() {
        return 'ft_timetrackerbundle_activitytype';
    }

}
