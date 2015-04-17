<?php

namespace FreelancerTools\InvoicingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class InvoiceType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\InvoicingBundle\Entity\Invoice',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('customer')
                //->add('invoiceDate')
                ->add('invoiceDate', 'datetime', array(
                    'label' => "Date",
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy"
                    )
                ))
                //->add('invoiceDueDate')
                ->add('invoiceDueDate', 'datetime', array(
                    'label' => "Due Date",
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy"
                    )
                ))
                ->add('status', 'choice', array(
                    'choices' => array(                        
                        '1' => 'Open',
                        '2' => 'Paid',
                        '3' => 'Cancelled',
                        '4' => 'Overdue'
                    )
                ))
                ->add('currency')
                /*
                ->add('currency', 'choice', array(
                    'choices' => array(
                        'CAD' => 'CAD',
                        'USD' => 'USD',                        
                    )
                ))*/
                ->add('invoiceNumber')
                ->add('showTimelog', null, array('required' => false))
                ->add('terms', null, array('attr' => array('rows' => 5)))
                ->add('items', 'collection', array('type' => new InvoiceItemType(), 'allow_add' => true,));
    }

    public function getName() {
        return 'ft_invoicingbundle_invoicetype';
    }

}
