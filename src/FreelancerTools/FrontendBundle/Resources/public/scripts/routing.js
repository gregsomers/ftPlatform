(function () {
    'use strict';
    angular.module('ftApp')
            .config(function ($routeProvider, $locationProvider, assetDir) {
                
                $routeProvider.when('/dashboard', {
                    controller: 'DashboardCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/dashboard.html',
                    resolve: {
                        invoice: function (DS) {
                            return  DS.findAll('invoices');
                        },
                        clients: function (DS) {
                            return DS.findAll('clients');
                        },
                        projects: function (DS) {
                            return DS.findAll('projects');
                        },                        
                    }
                });

                $routeProvider.when('/clients', {
                    controller: 'ClientListCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/clients.html',
                    resolve: {
                        clients: function(DS) {
                            return DS.findAll('clients');
                        }
                    }
                });

                $routeProvider.when('/client/new', {
                    controller: 'ClientNewCtrl',
                    templateUrl: assetDir + 'views/clients-new.html'
                });

                $routeProvider.when('/client/:id', {
                    controller: 'ClientEditCtrl',
                    templateUrl: assetDir + 'views/clients-edit.html'
                });

                $routeProvider.when('/timesheet', {
                    controller: 'TimesheetListCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/timesheet.html',
                    resolve: {
                        projects: function (DS) {
                            return DS.findAll('projects');
                        },
                        clients: function(DS) {
                            return DS.findAll('clients');
                        }
                    }
                });

                $routeProvider.when('/timesheet/:id', {
                    controller: 'TimesheetShowCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/timesheet-show.html',
                    resolve: {
                        project: function (DS, $route) {
                            return DS.find('projects', $route.current.params.id);
                        }
                    }
                });

                $routeProvider.when('/timesheet/slice/:id', {
                    controller: 'TimesheetSliceEditCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/timesheet-slice-edit.html'
                });

                $routeProvider.when('/invoices', {
                    controller: 'InvoiceListCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/invoices.html',
                    resolve: {
                        invoices: function (DS) {
                            return DS.findAll('invoices');
                        }
                    }
                });

                $routeProvider.when('/invoices/create', {
                    controller: 'InvoiceCreateCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/invoice-show.html',
                    resolve: {
                        clients: function (DS) {
                            return DS.findAll('clients');
                        },
                        projects: function (DS) {
                            return DS.findAll('projects');
                        },
                        currencies: function (DS) {
                            return DS.findAll('currencies');
                        }
                    }
                });

                $routeProvider.when('/invoices/:id/edit', {
                    controller: 'InvoiceShowCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/invoice-show.html',
                    resolve: {
                        invoice: function ($route, DS) {
                            return  DS.find('invoices', $route.current.params.id);
                        },
                        clients: function (DS) {
                            return DS.findAll('clients');
                        },
                        projects: function (DS) {
                            return DS.findAll('projects');
                        },
                        currencies: function (DS) {
                            return DS.findAll('currencies');
                        }
                    }
                });


                $routeProvider.when('/payments', {
                    controller: 'PaymentsListCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/payments.html',
                    resolve: {
                        invoices: function (DS) {
                            return DS.findAll('invoices');
                        }
                    }
                });


                $routeProvider.when('/invoices/:id/email', {
                    controller: 'InvoiceEmailCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/invoice-email.html',
                    resolve: {
                        emailtemplates: function(DS) {
                            return DS.findAll('emailtemplates');
                        },
                        invoice: function ($route, DS) {
                            return  DS.find('invoices', $route.current.params.id);
                        },
                    }
                });

                $routeProvider.when('/settings', {
                    controller: 'SettingsEditCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/settings.html',
                    resolve: {
                        settings: function(DS) {
                            return DS.findAll('settings');
                        }
                    }
                });
                
                $routeProvider.when('/currencies', {
                    controller: 'CurrenciesCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/currencies.html',
                    resolve: {
                        settings: function(DS) {
                            return DS.findAll('currencies');
                        }
                    }
                });
                
                $routeProvider.when('/paymentMethods', {
                    controller: 'PaymentMethodsCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/paymentMethods.html',
                    resolve: {
                        settings: function(DS) {
                            return DS.findAll('paymentmethods');
                        }
                    }
                });
                
                $routeProvider.when('/emailTemplates', {
                    controller: 'EmailTemplateEditCtrl',
                    controllerAs: 'vm',
                    templateUrl: assetDir + 'views/emailTemplates.html',
                    resolve: {
                        settings: function(DS) {
                            return DS.findAll('emailtemplates');
                        }
                    }
                });

                $locationProvider.html5Mode(false);

            });
})();