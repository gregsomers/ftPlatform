<?php

namespace FreelancerTools\InvoicingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class EmailInvoiceType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        /*
          $resolver->setDefaults(
          array(
          'data_class' => 'FreelancerTools\InvoicingBundle\Entity\Invoice',
          )
          );
         * 
         */
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('name', 'text')
                ->add('email', 'text')
                ->add('to', 'text')
                ->add('to', 'text')
                ->add('cc', 'text', array('required' => false))
                ->add('bcc', 'text', array('required' => false))
                ->add('subject', 'text')
                ->add('body', 'ckeditor', array(
                    'required' => false,
                    'attr' => array('rows' => 15),
                    'config' => array(
                        'toolbar' => array(
                            array(
                                'name' => 'document',
                                'items' => array('Source'),
                            ),
                            //'/',
                            array(
                                'name' => 'basicstyles',
                                'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                            ),
                        ),
                        'uiColor' => '#ffffff',
                    )
                        )
        );
    }

    public function getName() {
        return 'ft_invoicingbundle_emailinvoicetype';
    }

}
