<?php

namespace FreelancerTools\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PaymentType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\PaymentBundle\Entity\Payment',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('method')
                ->add('amount')  
                ->add('invoice')  
                ->add('date', 'datetime', array(
                    'label' => "Payment Date",
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd'                    
                ))
                ->add('notes', null, array('required' => false))      
                ->add('notes', null, array('required' => false))       
                ->add('emailNotification', 'checkbox', array('required' => false, 'mapped' => false))
                ;
                   
    }

    public function getName() {
        return 'ft_invoicingbundle_paymenttype';
    }

}
