(function () {
    'use strict';

    angular.module('ftApp')

            .controller('ReceiptsListCtrl', function ($scope, DS, Upload, $modal, assetDir) {
                $scope.receipts = DS.getAll('receipts');
                $scope.$watch(function () {
                    return DS.lastModified('receipts');
                }, function () {
                    $scope.receipts = DS.getAll('receipts');
                });

                $scope.$watch('files', function () {
                    $scope.upload($scope.files);
                });

                $scope.upload = function (files) {
                    if (files && files.length) {
                        for (var i = 0; i < files.length; i++) {
                            var file = files[i];
                            Upload.upload({
                                url: '/api/v1/receipts',
                                fields: {},
                                file: file
                            }).progress(function (evt) {
                                var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                                console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
                            }).success(function (data, status, headers, config) {
                                console.log('file ' + config.file.name + 'uploaded. Response: ' + data);
                                DS.inject('receipts', data);
                            });
                        }
                    }
                };

                $scope.edit = function (receipt) {
                    console.log("rec %o",receipt);

                    $scope.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/receipt-edit.html',
                        controller: 'ReceiptModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            receipt: function () {
                                return receipt;
                            }
                        }
                    });
                    $scope.modalInstance.result.then(function (receipt) {
                        receipt.DSSave();
                    }, function () {

                    });

                };
                
                $scope.remove = function(receipt) {
                    receipt.DSDestroy();
                };


            })
            
            .controller('ReceiptModalInstanceCtrl', function ($modalInstance, receipt) {
                var modal = this;
                modal.receipt = receipt;
                //modal.receipt.total = modal.receipt.total / 100; //set to decinal for display                

                modal.ok = function () {
                    //modal.receipt.total = modal.receipt.total * 100; //set to integer for database
                    $modalInstance.close(modal.receipt);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };

            })


            ;
})();


