<?php

namespace FreelancerTools\ReportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FreelancerTools\CoreBundle\Entity\Customer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ReportType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\ReportBundle\Entity\Report',
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('customer', 'entity', array(
                    'class' => 'FreelancerToolsCoreBundle:Customer',
                    'property' => 'name',
                    'required' => false,
                    'empty_value' => '',
                    'empty_data' => null
                ))
                /*
                  ->add('project', 'entity', array(
                  'class' => 'FreelancerToolsTimeTrackerBundle:Project',
                  'property' => 'name',
                  'required' => false,
                  'empty_value' => '',
                  'empty_data' => null
                  )) */
                ->add('start', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    //http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('end', 'datetime', array(
                    'required' => false,
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy h:mm:ss a',
                    'attr' => array(
                        'placeholder' => "mm/dd/yyyy h:m:s"
                    )
                ))
                ->add('invoiced', null, array('required' => false))
        //->add('service')
        ;



        $formModifier = function (FormInterface $form, Customer $customer = null) {
            $projects = null === $customer ? array() : $customer->getProjects();

            $form->add('projects', 'entity', array(
                'class' => 'FreelancerToolsTimeTrackerBundle:Project',
                'property' => 'name',
                'required' => false,
                'empty_value' => '',
                'expanded' => true,
                'multiple' => true,                
                'empty_data' => null,
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
        return 'ft_reportbundle_reporttype';
    }

}
