(function () {
    'use strict';

    var app = angular.module('ftApp', ['ngRoute', 'angular-loading-bar', 'ngMessages', 'ui.bootstrap', 'js-data', 'ngToast']);
    app.constant('assetDir', '/bundles/freelancertoolsfrontend/');
    app.config(function (DSProvider, cfpLoadingBarProvider) {
        DSProvider.defaults.basePath = '/api/v1';
        cfpLoadingBarProvider.parentSelector = 'section';
    });

    


})();