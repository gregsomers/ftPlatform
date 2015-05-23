(function () {
    'use strict';

    angular.module('ftApp')

            .controller('ActiveTimesliceCounterCtrl', function (DS, $location, $global) {
                var vm = this;
                vm.global = $global;

                if ($global.getActiveSliceId()) {
                    vm.activeSlice = $global.getActiveSlice();

                }


                vm.goToTimesheet = function (id) {
                    console.log("goToTimesheet " + id);
                    $location.url('/timesheet/' + id);
                };
                /*
                 vm.$watch(function () {
                 return $global.getActiveSlice();
                 }, function (newVal) {
                 console.log('data changes into: ', newVal);
                 vm.slice = newVal;
                 }, false);     */
            })

            .controller('NavMenuCtrl', function ($location) {
                var vm = this;
                vm.isActive = function (viewLocation) {
                    var url = $location.path();
                    return url.indexOf(viewLocation) > -1;
                };
                vm.menuClass = function (page) {
                    var current = $location.path().substring(1);
                    return page === current ? "current" : "";
                };
            })

            .controller('HeaderCtrl', function ($location) {
                var vm = this;
                vm.isActive = function (viewLocation) {
                    var url = $location.path();
                    return url.indexOf(viewLocation) > -1;
                };
                vm.menuClass = function (page) {
                    var current = $location.path().substring(1);
                    return page === current ? "current" : "";
                };
            })


            .controller('EmailTemplateEditCtrl', function (DS, $location, ngToast, $global, $routeParams, $http) {
                var vm = this;
                vm.templates = {};
                vm.tabs = [
                    {},
                    {}
                ];

                var templates = DS.getAll('emailtemplates');
                templates.forEach(function (data) {
                    vm.templates[data.name] = data;
                });

                vm.save = function () {
                    angular.forEach(vm.templates, function (template, key) {
                        template.DSSave();
                    });
                };
            })

            


            .controller('SettingsEditCtrl', function (DS, $location, ngToast, $global, $routeParams, $http) {
                var vm = this;
                vm.settings = {};
                vm.tabs = [
                    {},
                    {}
                ];

                var settings = DS.getAll('settings');
                settings.forEach(function (data) {
                    vm.settings[data.idString] = data;
                });

                vm.save = function () {
                    angular.forEach(vm.settings, function (setting, key) {
                        if (setting.idString === 'email_password') {
                            if (setting.value) {
                                //console.log("updating password");
                                setting.DSSave();
                            }
                        } else {
                            setting.DSSave();
                        }
                    });
                };
            })

            ;
})();