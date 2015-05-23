<?php

namespace FreelancerTools\CoreBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\CoreBundle\Form\SettingType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * @Route("/api/v1/settings")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class SettingsAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="settings_api_get")
     * @Method("GET")     
     */
    public function indexAction() {
        $settings = $this->getSettingRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser())
                ->getResults();
        
        foreach ($settings as $setting) {
            if($setting->getStringId() == 'email_password') {                
                $setting->setValue('');
            }
        }

        return $this->createView($settings, 200);
    }

    /**
     * @Route("/{id}", name="settings_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $namespce = substr($id, 0, strpos($id, '_'));
        $name = substr($id, strpos($id, '_') + 1);

        $setting = $this->getSettingRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('namespace', $namespce)
                ->scopeByField('name', $name)
                ->getSingleResult();

        if (!$setting) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);

        if ($setting->getStringId() == "email_password") {
            $password = $this->get('ft.encryption')->encrypt($formData['value']);
            $formData['value'] = $password;
        }

        $form = $this->createForm(new SettingType(), $setting, array('csrf_protection' => false));

        if($setting->getStringId() == "email_password") {
            $setting->setValue('Password Updated.');
            return $this->createView($setting, 200);
        }
            
        return $this->saveForm($form, $formData);
    }

}
