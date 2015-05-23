(function () {
    'use strict';

    angular.module('ftApp')

            .controller('ClientListCtrl', function ($scope, DS, $location, $modal, assetDir) {
                var vm = this;
                vm.clients = DS.getAll('clients');
                $scope.$watch(function () {
                    return DS.lastModified('clients');
                }, function () {
                    vm.clients = DS.getAll('clients');
                });

                vm.fields = ['name'];

                vm.sort = function (field) {
                    vm.sort.field = field;
                    vm.sort.order = !vm.sort.order;
                };

                vm.sort.field = "name";
                vm.sort.order = false;

                vm.edit = function (client) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/client-edit.html',
                        controller: 'ClientModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            client: function () {
                                return client;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (client) {
                        client.DSSave();
                    }, function () {

                    });
                };

                vm.remove = function (client) {
                    client.DSDestroy();
                };

                vm.create = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/client-edit.html',
                        controller: 'ClientModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            client: function (DS) {
                                return DS.createInstance('clients');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (client) {
                        client.DSCreate();
                    }, function () {

                    });
                };
            })

            .controller('ClientModalInstanceCtrl', function ($modalInstance, client) {
                var modal = this;
                modal.client = client;

                modal.ok = function () {
                    $modalInstance.close(modal.client);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };

            })

            ;
})();


