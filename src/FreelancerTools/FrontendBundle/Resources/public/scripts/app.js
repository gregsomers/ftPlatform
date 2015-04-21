'use strict';

var assetDir = "/bundles/freelancertoolsfrontend/";

var app = angular.module('ftApp', ['ngRoute', 'angular-loading-bar', 'ngMessages', 'ui.bootstrap']);

app.config(function ($routeProvider, $locationProvider) {
    $routeProvider.when('/clients', {
        controller: 'ClientListCtrl',
        templateUrl: assetDir + 'views/clients.html'
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
        templateUrl: assetDir + 'views/timesheet.html'
    });

    $routeProvider.when('/timesheet/:id', {
        controller: 'TimesheetShowCtrl',
        templateUrl: assetDir + 'views/timesheet-show.html'
    });

    $locationProvider.html5Mode(false);

});


app.filter('myDateFormat', function myDateFormat($filter) {
    return function (text) {
        var tempdate = new Date(text.replace(/-/g, "/"));
        return $filter('date')(tempdate, "MMM-dd-yyyy");
    };
});

app.filter('secondsToDateFormat', function myDateFormat($filter) {
    return function (text) {
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
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        var time = hours + ':' + minutes + ':' + seconds;
        return time;
    }
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