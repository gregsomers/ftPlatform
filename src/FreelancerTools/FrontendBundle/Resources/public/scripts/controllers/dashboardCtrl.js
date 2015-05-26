(function () {
    'use strict';

    angular.module('ftApp')

            .controller('DashboardCtrl', function (DS, $modal, assetDir) {
                var vm = this;
                vm.receivables = 0;
                vm.paid = 0;
                vm.unbilledTime = 0;
                vm.valueUnbilledTime = 0;
                vm.invoices = DS.getAll('invoices');
                vm.projects = DS.getAll('projects');

                var invoiceDate;
                var nextYear = (moment().year() - 1) + '-12-31';
                var lastYear = (moment().year()+1) + '-01-01';
                vm.invoices.forEach(function (invoice) {
                    invoiceDate = moment(invoice.invoiceDate);                    
                    if (invoiceDate.isBefore(lastYear,'year') && invoiceDate.isAfter(nextYear, 'year')) {
                        vm.receivables += invoice.getBalance();
                        vm.paid += invoice.getPaid();
                    }
                });
                
                vm.projects.forEach(function (project) {
                    if(project.isActive()) {
                        vm.unbilledTime += project.getUnbilledTime();
                        vm.valueUnbilledTime += project.getUnbilledValue();
                        //console.log("proj %o val %o",project,project.getUnbilledValue());
                    }
                });

            })


            ;
})();


