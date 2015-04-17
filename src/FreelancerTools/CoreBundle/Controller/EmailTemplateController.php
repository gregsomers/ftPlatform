<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\CoreBundle\Form\EmailTemplatesType;
use Symfony\Component\HttpFoundation\Request;

class EmailTemplateController extends Controller {

    /**
     * @Route("/settings/emailtemplates", name="email_templates")
     * @Template()
     */
    public function indexAction() {

        foreach ($this->getTemplateStorage()->findBy(array('user' => $this->getUser())) as $template) {
            $templates[str_replace(' ', '_', $template->getName())] = $template;
        }

        $form = $this->createForm(new EmailTemplatesType($templates));

        return array(
            'form' => $form,
        );
    }

    /**
     * @Route("/settings/emailtemplates/update", name="email_templates_update")
     * @Template("FreelancerToolsCoreBundle:EmailTemplate:index.html.twig")
     */
    public function updateAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        foreach ($this->getTemplateStorage()->findBy(array('user' => $this->getUser())) as $template) {
            $templates[str_replace(' ', '_', $template->getName())] = $template;
        }

        $form = $this->createForm(new EmailTemplatesType($templates))->bind($request);

        $data = $form->all();

        foreach ($data as $template) {
            $this->getDoctrine()->getManager()->persist($template->getData());
        }
        $this->getDoctrine()->getManager()->flush();
        
        return $this->redirect($this->generateUrl('email_templates'));        
    }

    protected function getTemplateStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\CoreBundle\Entity\EmailTemplate');
    }

}
