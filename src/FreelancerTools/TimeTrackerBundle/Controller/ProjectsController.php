<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\TimeTrackerBundle\Form\ProjectType;
use FreelancerTools\TimeTrackerBundle\Form\ProjectInvoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FreelancerTools\InvoicingBundle\Entity\InvoiceItem;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/projects")
 */
class ProjectsController extends Controller {

    /**
     * @Route("/", name="projects")
     * @Template()
     */
    public function indexAction() {
        $projects = $this->getProjectStorage()->getRepo()->getProjectsByUser($this->getUser());        

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $projects, $this->get('request')->query->get('page', 1)/* page number */, 50/* limit per page */, array()
        );

        return array(
            'pagination' => $pagination
        );
    }

    /**
     * @Route("/project/{id}/{showArchived}", name="project_show", defaults={"showArchived": "0"})
     * @Template()
     */
    public function showAction($id, $showArchived) {        
        $project = $this->getProjectStorage()->getRepo()->getSingleProject($id);        
        $slices = $this->getTimesliceStorage()->getRepo()->getTimeslicesByProject($project);

        $paginator = $this->get('knp_paginator');
        $slicesPager = $paginator->paginate(
                $slices, $this->get('request')->query->get('page', 1), 10, array()
        );

        $form = $this->createForm(new ProjectInvoiceType($this->getUser(), $project->getCustomer()), null);

        return array(
            'project' => $project,
            'slicesPager' => $slicesPager,
            'addToInvoiceForm' => $form->createView()
        );
    }

    /**
     * @Route("/add", name="project_add")
     * @Template()
     */
    public function addAction() {

        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $this->getProjectStorage()->create());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/create", name="project_create")
     * @Template()
     */
    public function createAction(Request $request) {
        $project = $this->getProjectStorage()->create();
        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $project)->bind($request);

        if ($form->isValid()) {
            $project->setUser($this->getUser());
            $this->getProjectStorage()->update($project);            
            $this->get('session')->getFlashBag()->add(
                    'success', "Project has been created."
            );
            return $this->redirect($this->generateUrl('projects'));
        }
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/edit/{id}", name="project_edit")
     * @Template()
     */
    public function editAction($id) {
        $project = $this->getProjectStorage()->findOneBy(array('id' => $id));  

        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $project);
        return array(
            'form' => $form->createView(),
            'project' => $project
        );
    }

    /**
     * @Route("/{id}/update", name="project_update")
     * @Template("FreelancerToolsTimeTrackerBundle:Projects:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $project = $this->getProjectStorage()->findOneBy(array('id' => $id));  

        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $project);
        $form->bind($request);

        if ($form->isValid()) {
            $this->getProjectStorage()->update($project);            
            $this->get('session')->getFlashBag()->add(
                    'success', "Project has been updated."
            );
            return $this->redirect($this->generateUrl('projects'));
        }

        return array(
            'form' => $form->createView(),
            'project' => $project
        );
    }

    /**
     * @Route("/{id}/addToInvoice", name="project_addtoinvoice")
     * @Template("FreelancerToolsTimeTrackerBundle:Projects:edit.html.twig")
     */
    public function addtoInvoiceAction(Request $request, $id) {
        $project = $this->getProjectStorage()->getRepo()->getSingleProject($id);            

        $form = $this->createForm(new ProjectInvoiceType($this->getUser(), $project->getCustomer()), null)->bind($request);

        $invoice = $form->get('invoice')->getData();

        $settings = array();
        foreach ($this->getSettingRepository()->findByNamespace('invoice') as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }

        foreach ($project->getActivities() as $activity) {
            if ($activity->getBalanceSeconds() == 0) {
                continue;
            }
            $item = new InvoiceItem();
            $item->setInvoice($invoice);

            $service = 'None';
            if ($activity->getService()) {
                $service = $activity->getService()->getName();
            }

            $item->setProduct($service);
            $item->setDescription($activity->getDescription());

            if ($settings['qty_round'] == 'up') {
                $qty = ceil($activity->getBalanceSeconds() / 3600);
            } elseif ($settings['qty_round'] == 'noround') {
                $qty = $activity->getBalanceSeconds() / 3600;
            }

            $item->setQuantity($qty);
            $item->setPrice($activity->getRate());
            $item->setUser($this->getUser());

            $activity->setBalanceSlicesInvoices($invoice, $item);

            $this->getDoctrine()->getManager()->persist($item);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('project_show', array('id' => $id)));
    }
    
    /**
     * @Route("/projects/delete/{id}", name="project_delete")
     * 
     */
    public function deleteAction($id) {

        $project = $this->getProjectStorage()->getRepo()->getSingleProject($id);          

        if (!$project) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        
        $this->getProjectStorage()->delete($project);     

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
    }
    
    protected function getProjectStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Project');
    }
    
    protected function getTimesliceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Timeslice');
    }  

    public function getSettingRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Setting');
    }

}
