(function () {
    'use strict';

    angular.module('ftApp')
            .controller('PaymentMethodsCtrl', function ($scope, DS, ngToast, $global, $modal, assetDir) {
                var vm = this;
                vm.tabs = [
                    {},
                    {}
                ];
                vm.methods = DS.getAll('paymentmethods');
                $scope.$watch(function () {
                    return DS.lastModified('paymentmethods');
                }, function () {
                    vm.methods = DS.getAll('paymentmethods');
                });

                vm.edit = function (method) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/paymentMethods-edit.html',
                        controller: 'PaymentMethodsModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            method: function () {
                                return method;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (method) {
                        method.DSSave();
                    }, function () {
                        //dismissed
                    });
                };

                vm.create = function (method) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/paymentMethods-edit.html',
                        controller: 'PaymentMethodsModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            method: function () {
                                return DS.createInstance('paymentmethods');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (currency) {
                        currency.DSCreate();
                    }, function () {
                        //dismissed
                    });
                };

                vm.remove = function (method) {
                    method.DSDestroy().then(null,function(resp){
                        ngToast.create({
                            className: 'danger',
                            content: resp.data.errors
                        });
                    });
                }
            })

            .controller('PaymentMethodsModalInstanceCtrl', function ($modalInstance, method) {
                var modal = this;
                modal.method = method;

                modal.ok = function () {
                    $modalInstance.close(modal.method);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };

            })

})();