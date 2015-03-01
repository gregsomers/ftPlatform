<?php

namespace FreelancerTools\TimeTrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\User;
use FreelancerTools\CoreBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;

class ProjectInvoiceType extends AbstractType {    

    /**
     * @var User
     */
    protected $user;
    protected $customer;

    public function __construct(User $user, Customer $customer) {
        
        $this->user = $user;
        $this->customer = $customer;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        //$transformer = new TagTransformer($this->em, $this->user);

        $params['user'] = $this->user;
        $params['customer'] = $this->customer;

        $builder
                ->add('invoice', 'entity', array(
                    'required' => true,
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
                        ))


                ;
            }

            public function getName() {
                return 'ft_timetrackerbundle_projectinvoicetype';
            }

        }
        