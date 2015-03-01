<?php

namespace FreelancerTools\TimeTrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\User;
use FreelancerTools\CoreBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;

class TimesliceType extends AbstractType {

    protected $em;

    /**
     * @var User
     */
    protected $user;
    protected $customer;

    public function __construct($em, User $user, Customer $customer = null) {
        $this->em = $em;
        $this->user = $user;
        $this->customer = $customer;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\TimeTrackerBundle\Entity\Timeslice',
                //'csrf_protection' => false
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        //$transformer = new TagTransformer($this->em, $this->user);

        $params['user'] = $this->user;
        $params['customer'] = $this->customer;

        $builder
                ->add('duration')
                ->add('startedAt', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    //http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('stoppedAt', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('activity')
                ->add('notes')
                ->add('invoiced', null, array(
                    'required' => false)
                )
                ->add('invoicedAt', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('invoiceItem')
        ;

        if ($this->customer) {
            $builder->add('invoice', 'entity', array(
                'required' => false,
                'label' => 'Invoice',
                'class' => 'FreelancerToolsInvoicingBundle:Invoice',
                'query_builder' => function(EntityRepository $er) use ($params) {
                    return $er->createQueryBuilder('i')
                                    ->andWhere('i.user = :user')
                                    ->andWhere('i.customer = :customer')
                                    ->andWhere('i.status != 2')
                                    //->andwhere('p.id = :id')
                                    //->addOrderBy('s.startedAt','desc')
                                    ->setParameters(array(
                                        ':user' => $params['user']->getId(),
                                        ':customer' => $params['customer']->getId()
                                    ))
                    ;
                },
                        'property' => 'invoiceNumber',
                    ));
                } else {
                    $builder->add('invoice', 'hidden');
                }
            }

            public function getName() {
                return 'ft_timetrackerbundle_timeslicetype';
            }

        }
        