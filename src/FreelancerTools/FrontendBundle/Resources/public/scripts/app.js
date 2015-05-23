(function () {
    'use strict';

//var assetDir = "/bundles/freelancertoolsfrontend/";

    var app = angular.module('ftApp', ['ngRoute', 'angular-loading-bar', 'ngMessages', 'ui.bootstrap', 'js-data', 'ngToast']);
    app.constant('assetDir', '/bundles/freelancertoolsfrontend/');
    app.config(function (DSProvider, cfpLoadingBarProvider) {
        DSProvider.defaults.basePath = '/api/v1';
        cfpLoadingBarProvider.parentSelector = 'section';
    });

    app.filter('active', function () {
        return function (input, param) {
            var ret = [];
            if (!angular.isDefined(param))
                param = true;
            angular.forEach(input, function (v) {
                if (angular.isDefined(v.active) && v.active === param) {
                    ret.push(v);
                }
                if (angular.isDefined(v.archived) && v.archived === param) {
                    ret.push(v);
                }
            });
            return ret;
        };
    });
    
    app.filter('archived', function () {
        return function (input, param) {
            var ret = [];
            if (!angular.isDefined(param))
                param = true;
            angular.forEach(input, function (v) {
                if (angular.isDefined(v.active) && v.active === param) {
                    ret.push(v);
                }
                if (angular.isDefined(v.archived) && v.archived === param) {
                    ret.push(v);
                }
            });
            return ret;
        };
    });

    app.directive('expandableTextarea', function ($animate) {
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
    });

    app.filter('myDateFormat', function myDateFormat($filter) {
        return function (text) {
            //var tempdate = new Date(text.replace(/-/g, "/"));
            return moment(text).format("DD-MMM-YYYY");
            return $filter('date')(text, "dd-MMM-yyyy");
        };
    });

    app.controller('ConfirmationModalInstanceCtrl', function ($modalInstance, message) {
        var modal = this;
        modal.message = message;
        modal.ok = function () {
            $modalInstance.close();
        };

        modal.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    });

    app.directive('ngReallyClick', function ($modal, assetDir) {
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
    });


    app.directive('ftCkeditor', function () {
        return {
            restrict: "A",
            require: '?ngModel',
            link: function (scope, element, attrs, ngModel) {
                var editor, updateModel;

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
    });

    app.directive('datePicker', function () {
        return {
            restrict: "A",
            require: '?ngModel',
            link: function (scope, element, attrs, ngModel) {
                var format = 'YYYY-MM-D H:m:s';
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
    });
    app.directive('ftRealtimeclock', function ($timeout) {
        //return null;
        return {
            restrict: "A",
            require: 'ngModel',
            scope: {startedAt: '=startedAt'},
            link: function (scope, element, attrs, ngModel) {

                var update = function () {
                    if (ngModel.$viewValue.stoppedAt) {
                        console.log(scope);
                        scope.$apply(function () {
                            scope.activeSlice = null;
                        });
                        return;
                    }
                    ngModel.$modelValue.counter += 1;
                    $timeout(update, 1000);
                };

                ngModel.$render = function () {
                    if (ngModel.$viewValue) {
                        var startDate = moment(ngModel.$viewValue.startedAt);
                        ngModel.$modelValue.counter = moment().unix() - startDate.unix();
                        $timeout(update, 1000);
                    }
                };

            }
        };
    });


    app.filter('secondsToDateFormat', function myDateFormat($filter) {
        return function (text) {

            if (text === 0) {
                return "00:00";
            }

            var sec_num = parseInt(text, 10); // don't forget the second param
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            /*if (seconds < 10) {
             seconds = "0" + seconds;
             }*/
            var time = hours + ':' + minutes; //+ ':' + seconds;
            return time;
        }
    });

    app.filter('secondsToTimeFormat', function myDateFormat($filter) {
        return function (text) {
            if (text === 0) {
                return "00:00";
            }

            var duration = moment.duration(text, 'seconds');
            return duration.format("h:mm:ss", {trim: false});
        };
    });

    app.directive('formField', function ($timeout) {
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
    });

})();