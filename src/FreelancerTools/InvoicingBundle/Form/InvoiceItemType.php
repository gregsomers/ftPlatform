<?php

namespace FreelancerTools\InvoicingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class InvoiceItemType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\InvoicingBundle\Entity\InvoiceItem',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('product', null, array(
                    'label' => ' '
                ))
                ->add('description', null, array(
                    'label' => ' ',
                    'attr' => array(
                        'rows' => "1"
                    )
                ))
                ->add('quantity', null, array(
                    'label' => ' '
                ))
                ->add('price', null, array(
                    'label' => ' '
                ))
               

        ;
    }

    public function getName() {
        return 'ft_invoicingbundle_invoiceitemtype';
    }

}
