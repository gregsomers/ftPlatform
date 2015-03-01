<?php

namespace FreelancerTools\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function mainMenu(FactoryInterface $factory, array $options) {
        $request = $this->container->get('request');

        //https://gist.github.com/mrflory/2278437
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');
        $menu->addChild("Dashboard", array('route' => 'dashboard'));
        $menu->addChild("Clients", array('route' => 'customers'));
        //should probably use this instead: http://symfony.com/doc/current/cookbook/bundles/prepend_extension.html
        if (array_key_exists("FreelancerToolsInvoicingBundle", $this->container->getParameter('kernel.bundles'))) {
            $invoices = $menu->addChild("Invoices", array('route' => 'invoices'));
            if ($request->get('id')) {
                $invoices->addChild('Edit', array('route' => 'invoice_edit', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $invoices->addChild('Email', array('route' => 'invoice_email', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $invoices->addChild('Payment', array('route' => 'invoice_payment', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
            }
            $invoices->addChild('Add', array('route' => 'invoice_add'))->setAttribute('class', 'hide');
            $invoices->addChild('Recurring Invoices', array('route' => 'invoices_recurring'));
        }
        $invoices = $menu->addChild("Payments", array('route' => 'payments'));
        if (array_key_exists("FreelancerToolsTimeTrackerBundle", $this->container->getParameter('kernel.bundles'))) {
            //$menu->addChild("Activities", array('route' => 'activities'));
            $projects = $menu->addChild("Timesheet", array('route' => 'projects'));
            if ($request->get('id')) {
                $projects->addChild('Edit', array('route' => 'project_show', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $projects->addChild('Activity', array('route' => 'activity_show', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $projects->addChild('Activity Edit', array('route' => 'activity_edit', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $projects->addChild('Project Edit', array('route' => 'project_edit', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $projects->addChild('Timeslice Edit', array('route' => 'timeslice_edit', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $projects->addChild('Timeslice Add', array('route' => 'timeslice_add', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
            }
            $projects->addChild('Activity Add', array('route' => 'activity_add'))->setAttribute('class', 'hide');
            $projects->addChild('Project Add', array('route' => 'project_add'))->setAttribute('class', 'hide');
        }
        if (array_key_exists("FreelancerToolsReportBundle", $this->container->getParameter('kernel.bundles'))) {
            $menu->addChild("Report", array('route' => 'report'));
        }

        return $menu;
    }

}
