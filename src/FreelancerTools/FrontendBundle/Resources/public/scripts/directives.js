(function () {
    'use strict';

    angular.module('ftApp')

            .directive('ftCurrencyInt', function () {
                return {
                    require: 'ngModel',
                    link: function (scope, element, attrs, ngModelController) {
                        ngModelController.$parsers.push(function (data) {
                            //convert data from view format to model format
                            return data * 100; //converted
                        });

                        ngModelController.$formatters.push(function (data) {
                            //convert data from model format to view format
                            return data / 100; //converted
                        });
                    }
                }
            })

            .directive('ngReallyClick', function ($modal, assetDir) {
                return {
                    scope: {
                        ngReallyClick: "&" //function binding                
                    },
                    restrict: 'A',
                    link: function (scope, element, attrs) {
                        element.bind('click', function (e) {
                            e.stopPropagation();

                            var _this = this;
                            _this.message = "";
                            switch (attrs.ngReallyType) {
                                case "delete":
                                    _this.message = "Are you sure you want to delete this?";
                                    break;
                            }

                            this.modalInstance = $modal.open({
                                animation: true,
                                templateUrl: assetDir + 'views/confirm-action-modal.html',
                                controller: 'ConfirmationModalInstanceCtrl as modal',
                                size: '',
                                resolve: {
                                    message: function () {
                                        return _this.message;
                                    }
                                }
                            });
                            this.modalInstance.result.then(function () {
                                scope.ngReallyClick();
                            }, function () {
                                //dismissed
                            });

                        });
                    }
                };
            })


            .directive('ftCkeditor', function () {
                return {
                    restrict: "A",
                    require: '?ngModel',
                    link: function (scope, element, attrs, ngModel) {
                        var editor, updateModel;
                        CKEDITOR.env.isCompatible = true;
                        if (CKEDITOR.instances[attrs.id]) {
                            delete CKEDITOR.instances[attrs.id];
                        }
                        editor = CKEDITOR.replace(attrs.id,
                                {"toolbar": [{"name": "document", "items": ["Source"]},
                                        {"name": "basicstyles",
                                            "items": ["Bold", "Italic", "Underline", "Strike", "Subscript", "Superscript", "-", "RemoveFormat"]}],
                                    "uiColor": "#ffffff"});

                        editor.on('instanceReady', function () {
                            return editor.setData(ngModel.$viewValue);
                        });

                        updateModel = function () {

                            return scope.$apply(function () {
                                return ngModel.$setViewValue(editor.getData());
                            });
                        };

                        editor.on('change', updateModel);
                        editor.on('dataReady', updateModel);
                        editor.on('key', updateModel);
                        editor.on('paste', updateModel);
                        editor.on('selectionChange', updateModel);
                        return ngModel.$render = function () {
                            return editor.setData(ngModel.$viewValue);
                        };


                    }
                };
            })

            .directive('datePicker', function () {
                return {
                    restrict: "A",
                    require: '?ngModel',
                    link: function (scope, element, attrs, ngModel) {
                        var format = 'YYYY-MM-DD HH:mm:ss';
                        if (attrs.format) {
                            format = attrs.format;
                        }

                        var dp = $(element).datetimepicker({useSeconds: true, format: format});
                        var picker = dp.data('DateTimePicker');

                        var updateModel = function () {
                            picker.hide();
                            return scope.$apply(function () {
                                return ngModel.$setViewValue(dp.val());
                            });
                        };

                        dp.on('dp.change', updateModel);
                        dp.on('dp.show', function () {
                            picker.setDate(ngModel.$modelValue);

                        });
                    }
                };
            })
            .directive('ftRealtimeclock', function ($timeout) {
                return {
                    restrict: "A",
                    require: 'ngModel',
                    scope: {startedAt: '=startedAt'},
                    link: function (scope, element, attrs, ngModel) {

                        var update = function () {
                            if (!ngModel.$viewValue || ngModel.$viewValue.stoppedAt) {
                                return;
                            }
                            var startDate = moment(ngModel.$viewValue.startedAt);
                            ngModel.$modelValue.counter = moment().unix() - startDate.unix();
                            $timeout(update, 1000);
                        };

                        ngModel.$render = function () {
                            if (ngModel.$viewValue) {
                                update();
                            }
                        };

                    }
                };
            })

            .directive('formField', function ($timeout) {
                return {
                    restrict: 'EA',
                    templateUrl: assetDir + 'views/form-field.html',
                    repalce: true,
                    scope: {
                        record: '=', //2-way binding
                        field: '@',
                        live: '@',
                        required: '@'
                    },
                    link: function ($scope, element, attrs) {

                        $scope.blurUpdate = function () {
                            if ($scope.live !== 'false') {
                                //$scope.record.update();
                            }
                        };
                        var saveTimeout;

                        $scope.update = function () {
                            $timeout.cancel(saveTimeout);
                            saveTimeout = $timeout($scope.blurUpdate, 1000);
                        };


                    }


                };
            })

            .directive('expandableTextarea', function ($animate) {
                return {
                    restrict: "A",
                    link: function (scope, element, attrs) {
                        element.on('focus', function () {
                            $(element).animate({rows: 5}, 300);
                        });
                        element.on('blur', function () {
                            $(element).animate({rows: 1}, 300);
                        });
                    }
                };
            })




            ;


})();


