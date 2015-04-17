<?php

namespace FreelancerTools\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailTemplatesType extends AbstractType {

    private $templates;

    public function __construct($templates) {
        $this->templates = $templates;
    }    

    public function buildForm(FormBuilderInterface $builder, array $options) {      

        foreach ($this->templates as $key => $template) {
            $builder->add($key, new EmailTemplateType(), array(
                'data' => $template,                
            ));
        }
    }

    public function getName() {
        return 'ft_icorebundle_emailtemplatestype';
    }

}
