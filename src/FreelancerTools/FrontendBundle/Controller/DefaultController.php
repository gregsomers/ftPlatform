<?php

namespace FreelancerTools\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;

class DefaultController extends FreelancerAPIController {

    /**
     * @Route("/angular/", name="angular")
     * @Template()
     */
    public function indexAction() {

        $settings = $this->getSettingRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser());

        $returnArr = array();
        foreach ($settings->getResults() as $setting) {
            if ($setting->getNamespace() . "_" . $setting->getName() == 'email_password') {
                $setting->setValue('');
                $returnArr['settings'][] = $setting;
                continue;
            }
            $returnArr['settings'][] = $setting;
        }

        $returnArr['user'] = $this->getUser();


        $returnArr['activeSlice'] = $this->getTimesliceRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('duration', 0)
                ->getSingleResult();
        
        $returnArr['paymentMethods'] = $this->getPaymentMethodRepository()
                ->createCurrentQueryBuilder('pm')
                ->scopeByField('user', $this->getUser())                
                ->getResults();  
        
        $returnArr['currencies'] = $this->getCurrencyRepository()
                ->createCurrentQueryBuilder('c')
                ->scopeByField('user', $this->getUser())                
                ->getResults();  

        $data = $this->get('jms_serializer')->serialize($returnArr, 'json');

        return array('appInitData' => $data);
    }

}
