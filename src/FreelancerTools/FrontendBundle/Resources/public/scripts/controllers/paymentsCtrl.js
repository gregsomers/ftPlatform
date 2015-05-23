(function () {
    'use strict';

    angular.module('ftApp')

            .controller('PaymentsListCtrl', function ($scope, DS, $location, $modal, assetDir) {
                var vm = this;
                vm.payments = DS.getAll('payments');
                $scope.$watch(function () {
                    return DS.lastModified('payments');
                }, function () {
                    vm.payments = DS.getAll('payments');
                });

                vm.fields = ['name'];

                vm.sort = function (field) {
                    vm.sort.field = field;
                    vm.sort.order = !vm.sort.order;
                };

                vm.sort.field = "date";
                vm.sort.order = true;

                vm.edit = function (payment) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/payment-show.html',
                        controller: 'PaymentModalInstanceCtrl as modal',
                        size: 'lg',
                        resolve: {
                            invoice: function () {
                                return null;
                            },
                            payment: function () {
                                return payment;
                            },
                            paymentMethods: function () {
                                return DS.findAll('paymentmethods');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (payment) {
                        payment.DSSave();
                    }, function () {
                        //dismissed
                    });

                };
                vm.remove = function (payment) {
                    payment.DSDestroy();
                };

            })

            .controller('PaymentModalInstanceCtrl', function (DS, $modalInstance, invoice, payment, paymentMethods) {
                var modal = this;
                modal.invoice = invoice;

                modal.payment = payment;
                modal.paymentMethods = DS.getAll('paymentmethods');
                
                if (invoice) {
                    modal.payment.amount = invoice.getBalance();
                    modal.payment.invoice_id = invoice.id;
                }
                modal.payment.date = moment().format('YYYY-MM-DD');

                modal.ok = function () {
                    $modalInstance.close(modal.payment);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
            })



            ;
})();


