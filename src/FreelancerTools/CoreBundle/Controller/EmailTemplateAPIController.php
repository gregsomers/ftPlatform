<?php

namespace FreelancerTools\CoreBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\CoreBundle\Form\EmailTemplateType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\PaymentBundle\Entity\Payment;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/emailtemplates")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class EmailTemplateAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="email_templates_api_get")
     * @Method("GET")    
     */
    public function indexAction() {
        $templates = $this->getTemplateRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
        ;

        return $this->createView($templates->getResults(), 200);
    }
    
    
    
    /**
     * @Route("/{id}", name="email_templates_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {       

        $template = $this->getTemplateRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$template) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new EmailTemplateType(), $template, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/render", name="email_templates_api_render")
     * @Method("POST")     
     */
    public function renderAction(Request $request) {
        
        $data = json_decode($request->getContent(), true);  
        
        $template = $this->getTemplateRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $data['template_id'])
                ->getSingleResult();       
        
        
        $invoice = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $data['invoice_id'])
                ->getSingleResult();
        
        if(!$template || !$invoice) {
            return $this->createView(array(), 404);
        }
        
        $template = $template->getBody();
        
        $this->get('ft.email_token_transformer')->transform($template);
        $twig = clone $this->get('twig');
        $twig->setLoader(new \Twig_Loader_String());
        $html = $twig->render(
                $template, array('invoice' => $invoice, 'user' => $this->getUser(), 'payment' => new Payment())
        );
        

        return $this->createView($html, 200);
    }

}
