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
                ->add('date', 'datetime', array(
                    'label' => "Payment Date",
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy"
                    )
                ))
                ->add('notes', null, array('required' => false))               
                ;
                   
    }

    public function getName() {
        return 'ft_invoicingbundle_paymenttype';
    }

}
