<?php

namespace FreelancerTools\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReceiptType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\PaymentBundle\Entity\Receipt',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('merchant')                
                ->add('date', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd'
                ))
                ->add('total')
                ->add('notes')    
                ->add('file', 'file')


        ;
    }

    public function getName() {
        return 'ft_receipt';
    }

}
