<?php

namespace FreelancerTools\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class SettingsType extends AbstractType {

    private $settings;

    public function __construct($settings) {
        $this->settings = $settings;
    }

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
                ->add('email_password_blank', 'text', array('required' => false))
                

        ;

        foreach ($this->settings as $key => $setting) {
            $builder->add($key, new SettingType(), array(
                'data' => $setting,
                
            ));
        }
    }

    public function getName() {
        return 'ft_icorebundle_settingstype';
    }

}
