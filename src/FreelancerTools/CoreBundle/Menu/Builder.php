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
        $menu->addChild("<i class='fa fa-tachometer'></i> Dashboard", array(
            'route' => 'dashboard',
            'attributes' => array('id' => 'nav-dashboard'),
            'extras' => array('safe_label' => true))
        );
        $menu->addChild("<i class='fa fa-user'></i> Clients", array('route' => 'customers', 'extras' => array('safe_label' => true)));
        //should probably use this instead: http://symfony.com/doc/current/cookbook/bundles/prepend_extension.html
        if (array_key_exists("FreelancerToolsInvoicingBundle", $this->container->getParameter('kernel.bundles'))) {
            $invoices = $menu->addChild("<i class='fa fa-file-text-o'></i> Invoices", array('route' => 'invoices', 'extras' => array('safe_label' => true)));
            if ($request->get('id')) {
                $invoices->addChild('Edit', array('route' => 'invoice_edit', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $invoices->addChild('Email', array('route' => 'invoice_email', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
                $invoices->addChild('Payment', array('route' => 'invoice_payment', 'routeParameters' => array('id' => $request->get('id'))))->setAttribute('class', 'hide');
            }
            $invoices->addChild('Add', array('route' => 'invoice_add'))->setAttribute('class', 'hide');
            //$invoices->addChild('Recurring Invoices', array('route' => 'invoices_recurring'));
        }
        $invoices = $menu->addChild("<i class='fa fa-usd'></i> Payments", array('route' => 'payments', 'extras' => array('safe_label' => true)));
        if (array_key_exists("FreelancerToolsTimeTrackerBundle", $this->container->getParameter('kernel.bundles'))) {
            //$menu->addChild("Activities", array('route' => 'activities'));
            $projects = $menu->addChild("<i class='fa fa-clock-o'></i> Timesheet", array(
                'route' => 'projects',
                'attributes' => array('id' => 'nav-timesheet'),
                'extras' => array('safe_label' => true))
            );

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
            $menu->addChild("<i class='fa fa-bar-chart'></i> Report", array('route' => 'report', 'extras' => array('safe_label' => true)));
        }

        return $menu;
    }

}
