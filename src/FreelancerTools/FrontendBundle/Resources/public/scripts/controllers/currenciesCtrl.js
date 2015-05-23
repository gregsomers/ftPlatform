(function () {
    'use strict';

    angular.module('ftApp')
            .controller('CurrenciesCtrl', function ($scope, DS, ngToast, $global, $modal, assetDir) {
                var vm = this;
                vm.tabs = [
                    {},
                    {}
                ];
                vm.currencies = DS.getAll('currencies');
                $scope.$watch(function () {
                    return DS.lastModified('currencies');
                }, function () {
                    vm.currencies = DS.getAll('currencies');
                });

                vm.edit = function (currency) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/currency-edit.html',
                        controller: 'CurrencyModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            currency: function () {
                                return currency;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (currency) {
                        currency.DSSave();
                    }, function () {
                        //dismissed
                    });
                };

                vm.create = function (currency) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/currency-edit.html',
                        controller: 'CurrencyModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            currency: function () {
                                return DS.createInstance('currencies');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (currency) {
                        currency.DSCreate();
                    }, function () {
                        //dismissed
                    });
                };

                vm.remove = function (currency) {
                    currency.DSDestroy();
                }
            })

            .controller('CurrencyModalInstanceCtrl', function ($modalInstance, currency) {
                var modal = this;
                modal.currency = currency;

                modal.ok = function () {
                    $modalInstance.close(modal.currency);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };

            })

})();