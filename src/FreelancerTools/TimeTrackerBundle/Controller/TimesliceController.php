<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FreelancerTools\TimeTrackerBundle\Form\TimesliceType;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/activities")
 */
class TimesliceController extends Controller {

    /**
     * @Route("/{id}/timeslice/add", name="timeslice_add")
     * @Template()
     */
    public function addAction($id) {
        $slice = $this->getTimesliceStorage()->create();
        $activity = $this->getActivityStorage()->findOneBy(array('id' => $id));
        $slice->setActivity($activity);

        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser()), $slice);

        return array(
            'form' => $form->createView(),
            'id' => $id
        );
    }

    /**
     * @Route("/{id}/timeslice/create", name="timeslice_create")
     * @Template("FreelancerToolsTimeTrackerBundle:Timeslice:add.html.twig")
     */
    public function createAction(Request $request, $id) {
        $slice = $this->getTimesliceStorage()->create();
        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser()), $slice);
        $form->bind($request);

        if ($form->isValid()) {
            $slice->setUser($this->getUser());
            $this->getTimesliceStorage()->update($slice);           
            $this->get('session')->getFlashBag()->add(
                    'success', "Timeslice has been created."
            );
            return $this->redirect($this->generateUrl('project_show', array('id' => $slice->getActivity()->getProject()->getId())));
        }

        return array(
            'form' => $form->createView(),
            'id' => $id
        );
    }

    /**
     * @Route("/timeslice/{id}", name="timeslice_edit")
     * @Template()
     */
    public function editAction($id) {
        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));    

        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser(), $slice->getActivity()->getCustomer()), $slice);
        return array(
            'form' => $form->createView(),
            'slice' => $slice
        );
    }
    
    /**
     * @Route("/{id}/timeslice/update", name="timeslice_update")
     * @Template("FreelancerToolsTimeTrackerBundle:Timeslice:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));            
       
        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser(), $slice->getActivity()->getCustomer()), $slice)->bind($request);

        if ($form->isValid()) {
            $slice->setUser($this->getUser());
            $this->getTimesliceStorage()->update($slice);
            $this->get('session')->getFlashBag()->add(
                    'success', "Timeslice has been updated."
            );
            return $this->redirect($this->generateUrl('project_show', array('id' => $slice->getActivity()->getProject()->getId())));
        }
        return array(
            'form' => $form->createView(),
            'slice' => $slice
        );
    }
    
    /**
     * @Route("/timeslice/delete/{id}", name="timeslice_delete")
     * 
     */
    public function deleteAction($id) {

        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));       

        if (!$slice) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        
        $this->getTimesliceStorage()->delete($slice);     

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
    }
    
    protected function getTimesliceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Timeslice');
    }
    
    protected function getActivityStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Activity');
    }
}
