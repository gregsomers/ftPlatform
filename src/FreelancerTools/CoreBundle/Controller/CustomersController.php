<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Form\CustomerType;
use FreelancerTools\CoreBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/customers")
 * 
 */
class CustomersController extends Controller {

    /**
     * @Route("/api", name="customers_api_index")
     * @Template()
     * @Method("GET")
     */
    public function apiIndexAction() {
        $customers = $this->getCustomerRepository()->findAll();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($customers, 'json');
        //$data = $serializer->deserialize($inputStr, $typeName, $format);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    /**
     * @Route("/api", name="customers_api_create")
     * @Template()
     * @Method("POST")
     */
    public function apiCREATEAction(Request $reuqest) {
        $customer = new Customer();
        
        $data = json_decode($reuqest->getContent(), true);
        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $customer, array('csrf_protection' => false));
        foreach ($data as $key => $value) {
            if (!$form->has($key)) {
                unset($data[$key]);
            }
        }
        
        $form->bind($data);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($customer);

            $response = new Response(json_encode(array('error' => 0)));
        } else {
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
     * @Route("/api/{id}", name="customers_api_index_post")
     * @Template()
     * @Method("POST")
     */
    public function apiPOSTAction(Request $reuqest, $id) {
        $customer = $this->getCustomerRepository()->findOneById($id);

        $data = json_decode($reuqest->getContent(), true);

        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $customer, array('csrf_protection' => false));

        foreach ($data as $key => $value) {
            if (!$form->has($key)) {
                unset($data[$key]);
            }
        }

        $form->bind($data);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($customer);

            $response = new Response(json_encode(array('error' => 0)));
        } else {
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
     * @Route("/api/{id}", name="customers_api_index_DEL")
     * @Template()
     * @Method("DELETE")
     */
    public function apiDELAction($id) {
        $customer = $this->getCustomerRepository()->findOneById($id);

        $this->getDoctrine()->getManager()->remove($customer);
        //$this->getDoctrine()->getManager()->flush();
        
        $response = new Response(json_encode(array('error' => 0)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/{id}", name="customers_api_id")
     * @Template()
     */
    public function apiIndex1Action($id) {
        $customers = $this->getCustomerRepository()->findOneById($id);

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($customers, 'json');
        //$data = $serializer->deserialize($inputStr, $typeName, $format);


        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/", name="customers")
     * @Template()
     */
    public function indexAction() {
        $customers = $this->getCustomerRepository()->findAll();
        //$customers->createCurrentQueryBuilder('c');                
        return array('customers' => $customers);
    }

    /**
     * @Route("/add", name="customer_add")
     * @Template()
     */
    public function addAction() {

        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), new Customer());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/create", name="customer_create")
     * @Template()
     */
    public function createAction(Request $request) {
        $customer = new Customer();
        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $customer);
        $form->bind($request);

        if ($form->isValid()) {
            $customer->setUser($this->getUser());
            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add(
                    'success', "Customer has been created."
            );
            return $this->redirect($this->generateUrl('customer_edit', array('id' => $customer->getId())));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="customer_edit")
     * @Template()
     */
    public function editAction($id) {
        $customer = $this->getCustomerRepository()->findOneById($id);

        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $customer);

        return array(
            'form' => $form->createView(),
            'customer' => $customer
        );
    }

    /**
     * @Route("/submit/{id}", name="customer_submit")
     * @Template()
     */
    public function submitAction(Request $request, $id) {
        $customer = $this->getCustomerRepository()->findOneById($id);
        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $customer);
        $form->bind($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add(
                    'success', "Customer has been updated."
            );
            return $this->redirect($this->generateUrl('customer_edit', array('id' => $id)));
        }
        return array(
            'form' => $form->createView(),
            'customer' => $customer
        );
    }

    /**
     * @Route("delete/{id}", name="customer_delete")
     * 
     */
    public function deleteAction($id) {

        $entity = $this->getCustomerRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        $this->getDoctrine()->getManager()->remove($entity);
        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * get customer repository
     *
     * @return CustomerRepository
     */
    public function getCustomerRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Customer');
    }

}
