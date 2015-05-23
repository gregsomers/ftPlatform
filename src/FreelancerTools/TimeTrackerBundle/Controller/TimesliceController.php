<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FreelancerTools\TimeTrackerBundle\Form\TimesliceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use DateTime;

/**
 * @Route("/activities")
 */
class TimesliceController extends Controller {
    
    /**
     * @Route("/timeslice/api/{id}", name="timeslice_edit_api")
     * @Method("GET")
     */
    public function apiEditAction($id) {
        //$this->getDoctrine()->getManager()->clear();
        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));
        //$this->getDoctrine()->getManager()->refresh($slice);
        
        //print_r($slice->getStartedAt());
        
        $response = new Response($this->get('jms_serializer')->serialize($slice, 'json'));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/timeslice/api/{id}", name="timeslice_del_api")
     * @Method("DELETE")
     */
    public function apiDelAction($id) {
        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));
        $this->getTimesliceStorage()->delete($slice);        
        
        $response = new Response(json_encode(array('error' => 0)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/timeslice/api/create", name="timeslice_create_api")
     * @Template("FreelancerToolsTimeTrackerBundle:Timeslice:edit.html.twig")
     * @Method("POST")
     */
    public function apiCreateAction(Request $request) {
        $data = json_decode($request->getContent(), true);  
        $activity = $this->getActivityStorage()->findOneBy(array('id' => $data['id']));
        
        $timeslice = $this->getTimesliceStorage()
                ->create()
                ->setActivity($activity)
                ->setUser($this->getUser())
                ->setStartedAt(new DateTime('now'));
        $activity
                ->addTimeslice($timeslice)
                ->setUpdatedAt(new DateTime('now'));

        $this->getActivityStorage()->update($activity);
        $this->getTimesliceStorage()->update($timeslice);
        $this->getDoctrine()->getManager()->refresh($timeslice);
        
        $response = new Response($this->get('jms_serializer')->serialize($timeslice, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
        
    }
    
    /**
     * @Route("/timeslice/api/{id}", name="timeslice_update_api")
     * @Template("FreelancerToolsTimeTrackerBundle:Timeslice:edit.html.twig")
     * @Method("POST")
     */
    public function apiUpdateAction(Request $request, $id) {
        $slice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));   
        
        $data = json_decode($request->getContent(), true);        
       
        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser(), $slice->getActivity()->getCustomer()), $slice, array('csrf_protection' => false));
        
        foreach ($data as $key => $value) {
            if (!$form->has($key)) {
                unset($data[$key]);
            }
        }
        //can't change the activity after it's been associated
        $data['activity'] = $slice->getActivity()->getId();
     
        
        $form->bind($data);        
        
        
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($slice);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($slice);

            $response = new Response(json_encode(array('error' => 0)));
        } else {
            echo $form->getErrorsAsString();
            $errors = array();
            foreach ($form->getErrors() as $er) {
                $errors[] = $er->getMessage();
            }

            $response = new Response(json_encode(array('error' => 1, "errors" => $errors)));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
                
    }

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
