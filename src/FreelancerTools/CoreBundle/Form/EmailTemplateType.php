<?php

namespace FreelancerTools\CoreBundle\Form;

use FreelancerTools\CoreBundle\Entity\EmailTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class EmailTemplateType extends AbstractType {

    private $label;

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'FreelancerTools\CoreBundle\Entity\EmailTemplate',
                    'cascade_validation' => true
                )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
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
                ))
        ;
    }

    public function getName() {
        return 'ft_icorebundle_emailtemplatetype';
    }

}
