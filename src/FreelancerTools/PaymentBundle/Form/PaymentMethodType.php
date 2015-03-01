<?php

namespace FreelancerTools\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentMethodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            //->add('createdAt')
            //->add('updatedAt')
            //->add('user')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FreelancerTools\PaymentBundle\Entity\PaymentMethod'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'freelancertools_paymentbundle_paymentmethod';
    }
}
