<?php

namespace FreelancerTools\CoreBundle\Form;

use FreelancerTools\CoreBundle\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class SettingType extends AbstractType {

    private $label;

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\CoreBundle\Entity\Setting',
                    'cascade_validation' => true
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof Setting) {
                //$label = ucwords($data->getName());

                if ($data->getName() == 'security') {
                    $form->add($builder->getFormFactory()->createNamed(
                                    'value', 'choice', null, array(
                                'auto_initialize' => false,
                                'choices' => array(
                                    'none' => 'None',
                                    'ssl' => 'SSL',
                                    'tls' => 'TLS'
                                ))
                    ));
                }

                if ($data->getName() == 'default_terms') {
                    $form->add($builder->getFormFactory()->createNamed(
                                    'value', 'textarea', null, array(
                                'auto_initialize' => false,
                                'attr' => array('rows' => 5)
                                    )
                    ));
                }
                if ($data->getName() == 'payment_reminder') {
                    $form->add($builder->getFormFactory()->createNamed(
                                    'value', 'choice', null, array(
                                'auto_initialize' => false,
                                'choices' => array(
                                    '1' => 'Yes',
                                    '0' => 'No',
                                )
                                    )
                    ));
                }

                if ($data->getName() == 'qty_round') {
                    $form->add($builder->getFormFactory()->createNamed(
                                    'value', 'choice', null, array(
                                'auto_initialize' => false,
                                'choices' => array(
                                    'up' => 'Up to Nearest Whole Number',
                                    'noround' => 'No Rounding',
                                )
                                    )
                    ));
                }
            }
        });


        $builder
                //->add('name', 'text')
                ->add('value', 'text', array(
                    'label' => ''
                ))
        ;
    }

    public function getName() {
        return 'ft_icorebundle_settingstype';
    }

}
