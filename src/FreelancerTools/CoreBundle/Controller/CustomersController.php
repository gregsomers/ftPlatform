<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Form\CustomerType;
use FreelancerTools\CoreBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/customers")
 * 
 */
class CustomersController extends Controller {

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
