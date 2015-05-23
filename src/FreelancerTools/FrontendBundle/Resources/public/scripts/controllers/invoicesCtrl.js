(function () {
    'use strict';

    angular.module('ftApp')

            .controller('InvoiceListCtrl', function ($scope, DS, $location, ngToast) {
                var vm = this;
                vm.invoices = DS.getAll('invoices');
                $scope.$watch(function () {
                    return DS.lastModified('invoices');
                }, function () {
                    vm.invoices = DS.getAll('invoices');
                });

                vm.fields = ['name', 'client.name'];

                vm.edit = function (id) {
                    $location.url('/invoices/' + id + '/edit');
                };

                vm.create = function () {
                    $location.url('/invoices/create');
                };

                vm.delete = function (invoice) {
                    invoice.DSDestroy().then(function () {
                        var idx = vm.invoices.indexOf(invoice);
                        if (idx !== -1) {
                            vm.invoices.splice(idx, 1);
                        }
                        ngToast.create({
                            className: 'success',
                            content: 'Delete Successful'
                        });
                    });
                    ;
                };

                vm.sort = function (field) {
                    vm.sort.field = field;
                    vm.sort.order = !vm.sort.order;
                };

                vm.sort.field = "invoiceDate";
                vm.sort.order = true;
            })

            .controller('InvoiceShowCtrl', function (DS, $location, $routeParams, ngToast, $modal, assetDir, $http, $q) {
                var vm = this;
                vm.invoice = DS.get('invoices', $routeParams.id);
                vm.clients = DS.getAll('clients');
                vm.currencies = DS.getAll('currencies');
                vm.unbilledProjects = [];
                vm.unbilledProjects = [];

                vm.back = function () {
                    $location.url('/invoices');
                };
                vm.save = function () {
                    var promises = [];
                    vm.invoice.items.forEach(function (item) {
                        promises.push(item.DSSave());
                    });

                    var invoicePromis = vm.invoice.DSSave();
                    invoicePromis.then(function () {

                        ngToast.create({
                            className: 'success',
                            content: 'Update Successful'
                        });
                    });
                    promises.push(invoicePromis);
                    return promises;
                };
                vm.addInvoiceItem = function () {
                    DS.create('invoiceitems', {invoice_id: vm.invoice.id}).then(function (invoiceItem) {
                        DS.link('invoices', vm.invoice.id, ['invoiceitems']);
                        console.log(invoiceItem);
                        console.log(vm.invoice);
                    });
                };
                vm.deleteInvoiceItem = function (item) {
                    item.removeTimeslices();
                    item.DSDestroy();
                    vm.checkUnbilledWork();
                };
                vm.delete = function () {
                    vm.invoice.items.forEach(function (item) {
                        item.removeTimeslices();
                        item.DSDestroy();
                    });
                    vm.invoice.DSDestroy();
                    vm.back();
                };
                vm.sendCustomEmail = function () {
                    $location.url('/invoices/' + vm.invoice.id + '/email');
                };
                vm.sendEmail = function () {
                    $q.all(vm.save()).then(function () {
                        $http.post('/api/v1/invoices/email', {invoice_id: vm.invoice.id}).then(function () {
                            ngToast.create({
                                className: 'success',
                                content: 'Email Sent'
                            });
                        });
                    });
                };
                vm.addPayment = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/payment-show.html',
                        controller: 'PaymentModalInstanceCtrl as modal',
                        size: 'lg',
                        resolve: {
                            invoice: function () {
                                return vm.invoice;
                            },
                            payment: function () {
                                return DS.createInstance('payments');
                            },
                            paymentMethods: function () {
                                return DS.getAll('paymentmethods');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (payment) {
                        var emailNotification = payment.emailNotification;
                        DS.create('payments', payment).then(function (paymentResult) {
                            if (vm.invoice.getBalance() <= 0) {
                                vm.invoice.status = 2;
                                vm.invoice.DSSave().then(function () {
                                    if (emailNotification) {
                                        $http.post('/api/v1/payments/email', {payment_id: paymentResult.id}).then(function () {
                                            ngToast.create({
                                                className: 'success',
                                                content: 'Payment Email Sent to Client'
                                            });
                                        });
                                    }
                                });
                            }
                        });
                    }, function () {
                        //dismissed
                    });
                };
                vm.checkUnbilledWork = function () {
                    vm.unbilledProjects.splice(0, vm.unbilledProjects.length);
                    var projects = DS.filter('projects', {
                        where: {
                            client: {
                                '==': vm.invoice.client
                            }
                        }
                    });
                    projects.forEach(function (project) {
                        if (project.getUnbilledTime() > 0) {
                            vm.unbilledProjects.push(project);
                        }
                    });
                };
                vm.checkUnbilledWork(); //check for unbilled projects

                vm.addUnbilledProjects = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/invoice-unbilled.html',
                        controller: 'InvoiceUnbilledInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            projects: function () {
                                return vm.unbilledProjects;
                            },
                            invoice: function () {
                                return vm.invoice;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (data) {
                        vm.unbilledSlices = data.slices;
                        data.items.forEach(function (item) {
                            var slices = item.timeslices;
                            item.invoice_id = vm.invoice.id;
                            DS.create('invoiceitems', item).then(function (invoiceItem) {
                                if (slices) {
                                    slices.forEach(function (slice) {
                                        slice.invoiceItem = invoiceItem.id;
                                        slice.DSSave();
                                    });
                                }
                            });
                        });
                        DS.link('invoices', vm.invoice.id, ['invoiceitems']);
                        vm.checkUnbilledWork();
                    }, function () {
                        //dismissed
                    });
                };

            })

            .controller('InvoiceCreateCtrl', function ($scope, DS, $location, ngToast, $global, $modal, assetDir, $route) {
                var vm = this;

                vm.clients = DS.getAll('clients');
                vm.invoice = DS.createInstance('invoices');

                //defaults
                var nextId = $global.getAppSetting('invoice_next_id').value++;
                vm.invoice.invoiceNumber = $global.getAppSetting('invoice_prefix').value + moment().format("YYYYMM") + nextId;
                vm.invoice.currency_id = 0;
                vm.invoice.status = 1;
                vm.invoice.invoiceDate = moment().format("YYYY-MM-DD");
                vm.invoice.invoiceDueDate = moment().add(1, 'M').format("YYYY-MM-DD");
                vm.invoice.terms = $global.getAppSetting('invoice_default_terms').value;
                vm.invoice.items = [];
                vm.unbilledSlices = [];
                vm.unbilledProjects = [];


                $scope.$on('$routeChangeStart', function (next, current) {
                    //if the page is changed, uncommited changes need to be reverted, catch the change and revert. 
                    //Namely slices need to be marked not invoiced if they were added to this invoice
                    vm.invoice.items.forEach(function (item) {
                        vm.deleteInvoiceItem(item);
                    });
                });

                vm.back = function () {
                    $location.url('/invoices');
                };
                vm.save = function () {
                    DS.create('invoices', vm.invoice).then(function (invoice) {
                        $global.getAppSetting('invoice_next_id').DSSave(); //invoice saved, update the next id value back to the server                        
                        if (vm.invoice.items) {
                            vm.invoice.items.forEach(function (item) {
                                var slices = item.timeslices;
                                item.invoice_id = invoice.id;
                                DS.create('invoiceitems', item).then(function (item) {
                                    if (slices) {
                                        slices.forEach(function (slice) {
                                            slice.invoiceItem = item.id;
                                            slice.DSSave();
                                        });
                                    }
                                });
                            });
                        }
                        ngToast.create({
                            className: 'success',
                            content: 'Created Successfully'
                        });
                        $location.url('/invoices/' + invoice.id + '/edit');
                    });
                };
                vm.addInvoiceItem = function () {
                    var item = DS.createInstance('invoiceitems');
                    vm.invoice.items.push(item);
                };
                vm.deleteInvoiceItem = function (item) {
                    item.removeTimeslices(false);
                    vm.checkUnbilledWork();
                    //remove item from invoice, this has to be done manually because DS is not tracking the entity until persisted to the database
                    $global.arrayRemoveElm(vm.invoice.items, item);
                };

                vm.checkUnbilledWork = function () {
                    vm.unbilledProjects.splice(0, vm.unbilledProjects.length);
                    var projects = DS.filter('projects', {
                        where: {
                            client: {
                                '==': vm.invoice.client
                            }
                        }
                    });
                    projects.forEach(function (project) {
                        if (project.getUnbilledTime() > 0) {
                            vm.unbilledProjects.push(project);
                        }
                    });
                };

                vm.addUnbilledProjects = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/invoice-unbilled.html',
                        controller: 'InvoiceUnbilledInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            projects: function () {
                                return vm.unbilledProjects;
                            },
                            invoice: function () {
                                return vm.invoice;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (data) {
                        vm.unbilledSlices = data.slices;
                        data.items.forEach(function (item) {
                            vm.invoice.items.push(item);
                        });
                        vm.checkUnbilledWork();
                    }, function () {
                        //dismissed
                    });
                };
            })
            .controller('InvoiceUnbilledInstanceCtrl', function ($modalInstance, projects, invoice, DS) {
                var modal = this;
                modal.projects = projects;
                modal.sorting = "project";
                modal.date = moment().format('YYYY-MM-DD');

                modal.ok = function () {
                    var totalSeconds = 0;
                    modal.slices = [];
                    modal.items = [];

                    if (modal.sorting === "project") {
                        modal.project.timeslices.forEach(function (slice) {
                            if (!slice.isBilled() &&
                                    (moment(slice.stoppedAt).isBefore(modal.date, 'day') ||
                                            moment(slice.stoppedAt).isSame(modal.date, 'day')
                                            ))
                            {
                                totalSeconds += slice.duration;
                                slice.invoiceItem = true; //placeholder until real ID is generated
                                modal.slices.push(slice);
                            }

                        });
                        //console.log("Time left on project %o", modal.project.getUnbilledTime());                        
                        if (totalSeconds > 0) {
                            var item = DS.createInstance('invoiceitems');
                            item.description = modal.project.name;
                            item.product = modal.project.name;
                            //item.quantity = Math.round((totalSeconds / 3600) * 100) / 100; //round to 2 dc places
                            item.quantity = Math.ceil((totalSeconds / 3600)); //rounds up
                            item.price = modal.project.rate;
                            item.timeslices = modal.slices;
                            //invoice.items.push(item);
                            modal.items.push(item);
                            //console.log("item from modal %o",item);
                        }
                    } else if (modal.sorting === "activity") {

                        modal.project.activities.forEach(function (activity) {
                            activity.timeslices.forEach(function (slice) {
                                if (!slice.isBilled() &&
                                        (moment(slice.stoppedAt).isBefore(modal.date, 'day') ||
                                                moment(slice.stoppedAt).isSame(modal.date, 'day')
                                                ))
                                {
                                    totalSeconds += slice.duration;
                                    slice.invoiceItem = true; //placeholder until real ID is generated
                                    modal.slices.push(slice);
                                }
                            });

                            if (totalSeconds > 0) {
                                var item = DS.createInstance('invoiceitems');
                                item.description = activity.description;
                                item.product = activity.description;
                                item.quantity = Math.round((totalSeconds / 3600) * 100) / 100; //round to 2 dc places
                                item.price = (activity.rate) ? activity.rate : modal.project.rate;
                                item.timeslices = modal.slices;
                                //invoice.items.push(item);
                                modal.items.push(item);
                            }
                            //reset for the next activity
                            totalSeconds = 0;
                            modal.slices = [];

                        });
                    }
                    //send the slices, they'll be marked as invoiced and linked after the invoice is saved
                    $modalInstance.close({items: modal.items, slices: modal.slices, project: modal.project});
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
            })
            .controller('InvoiceEmailCtrl', function (DS, $location, ngToast, $global, $routeParams, $http, emailtemplates, invoice) {
                var vm = this;
                var id = $routeParams.id;
                vm.data = {};

                vm.templates = DS.getAll('emailtemplates');
                vm.invoice = invoice;
                vm.data.invoice_id = invoice.id;
                vm.data.to = vm.invoice.client.emailAddress;
                vm.data.subject = "Invoice #" + vm.invoice.invoiceNumber;
                vm.data.name = $global.getCurrentUser().nameToString;
                vm.data.email = $global.getCurrentUser().email;

                vm.renderTemplate = function () {
                    $http.post('/api/v1/emailtemplates/render', {template_id: vm.data.template, invoice_id: vm.invoice.id}).then(function (renderedTemplate) {
                        //console.log(renderedTemplate.data);                        
                        vm.data.body = renderedTemplate.data;
                    });
                };

                vm.send = function () {
                    $http.post('/api/v1/invoices/emailCustom', vm.data).then(function (response) {
                        //console.log(renderedTemplate.data);      
                        ngToast.create({
                            className: 'success',
                            content: 'Email Sent'
                        });
                        vm.back();
                    });

                };

                vm.back = function () {
                    $location.url('/invoices/' + id + '/edit');
                };

            })


            ;

})();


