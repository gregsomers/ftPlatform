(function () {
    'use strict';

    angular.module('ftApp')

            .controller('TimesheetListCtrl', function ($scope, $location, DS, $modal, assetDir) {
                var vm = this;
                vm.showActive = true;
                vm.projects = DS.getAll('projects');
                $scope.$watch(function () {
                    return DS.lastModified('projects');
                }, function () {
                    vm.projects = DS.getAll('projects');
                });

                vm.fields = ['name', 'client.name'];
                vm.sort = function (field) {
                    vm.sort.field = field;
                    vm.sort.order = !vm.sort.order;
                };
                vm.sort.field = "name";
                vm.sort.order = false;
                
                vm.toggleActive = function(){
                    vm.showActive = !vm.showActive;
                    return true;
                };

                vm.edit = function (project) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/project-edit.html',
                        controller: 'ProjectModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            project: function () {
                                return project;
                            },
                            clients: function () {
                                return DS.findAll('clients');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (project) {
                        project.DSSave();
                    }, function () {
                        //dismissed
                    });

                };

                vm.create = function (project) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/project-edit.html',
                        controller: 'ProjectModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            project: function () {
                                var project = DS.createInstance('projects');
                                project.active = true;
                                return project;
                            },
                            clients: function () {
                                return DS.findAll('clients');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (project) {
                        project.DSCreate();
                    }, function () {
                        //dismissed
                    });

                };

                vm.show = function (id) {
                    $location.url('/timesheet/' + id);
                };
                vm.remove = function (project) {
                    project.DSDestroy();
                };
            })

            .controller('ProjectModalInstanceCtrl', function (DS, $modalInstance, project) {
                var modal = this;
                modal.project = project;
                modal.clients = DS.getAll('clients');

                modal.ok = function () {
                    $modalInstance.close(modal.project);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
            })

            .controller('TimesheetShowCtrl', function ($location, $routeParams, $filter, DS, ngToast, $modal, assetDir) {
                var vm = this;
                vm.showArchived = false;
                vm.project = DS.get('projects', $routeParams.id);
                console.log(vm.project);
                vm.tabs = [
                    {},
                    {}
                ];
                
                vm.toggleActive = function(){
                    vm.showArchived = !vm.showArchived;
                    return true;
                };
                
                vm.back = function () {
                    $location.url('/timesheet');
                };

                vm.start = function (activity) {
                    var d = $filter('date')(new Date(), 'yyyy-M-d HH:m:s');
                    DS.create('timeslices', {activity_id: activity.id, project_id: vm.project.id, startedAt: d}).then(function (slice) {
                        ngToast.create({
                            className: 'success',
                            content: 'Activity Started'
                        });
                        //$global.setActiveSlice(slice);
                    });
                    vm.tab[0].active = true;
                };
                
                vm.stop = function (slice) {
                    var d = $filter('date')(new Date(), 'yyyy-M-d HH:m:s');
                    slice.stoppedAt = d;
                    slice.DSSave().then(
                            function () {
                                //$global.clearActiveSlice();
                                ngToast.create({
                                    className: 'success',
                                    content: 'Timeslice Stopped'
                                });
                            },
                            function () {
                            }
                    );
                };   
                
                vm.edit = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/project-edit.html',
                        controller: 'ProjectModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            project: function () {
                                return vm.project;
                            },
                            clients: function () {
                                return DS.findAll('clients');
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (project) {
                        project.DSSave();
                    }, function () {
                        //dismissed
                    });
                };
                vm.addActivity = function () {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/activity-edit.html',
                        controller: 'ActivityModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            activity: function () {
                                return null;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (activity) {
                        DS.create('activities', {activity: activity, project_id: vm.project.id});
                        vm.tab[0].active = true;
                    }, function () {
                        //dismissed
                    });
                };
                vm.editActivity = function (activity) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/activity-edit.html',
                        controller: 'ActivityModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            activity: function () {
                                return activity;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (activity) {
                        console.log(activity);
                        activity.DSSave();
                        //vm.tab[1].active = true;
                    }, function () {
                        //dismissed
                    });
                };
                vm.editTimeslice = function (timeslice) {
                    vm.modalInstance = $modal.open({
                        animation: true,
                        templateUrl: assetDir + 'views/timeslice-edit.html',
                        controller: 'TimesliceModalInstanceCtrl as modal',
                        size: '',
                        resolve: {
                            timeslice: function () {
                                return timeslice;
                            }
                        }
                    });
                    vm.modalInstance.result.then(function (timeslice) {
                        timeslice.DSSave();
                    }, function () {
                        //dismissed
                    });
                };
                
                vm.removeSlice = function (slice) {
                    slice.DSDestroy();
                };
                
                vm.removeActivity = function (activity) {
                    activity.timeslices.forEach(function (slice) {
                        slice.DSDestroy();
                    });
                    activity.DSDestroy();
                };
            })
            .controller('ActivityModalInstanceCtrl', function ($modalInstance, activity) {
                var modal = this;
                if (activity) {
                    modal.activity = activity;
                } else {
                    modal.activity = {};
                }
                modal.ok = function () {
                    $modalInstance.close(modal.activity);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
            })
            .controller('TimesliceModalInstanceCtrl', function ($modalInstance, timeslice) {
                var modal = this;
                if (timeslice) {
                    modal.timeslice = timeslice;
                } else {
                    modal.timeslice = {};
                }
                modal.ok = function () {
                    $modalInstance.close(modal.timeslice);
                };

                modal.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
            })
            .controller('TimesheetSliceEditCtrl', function (DS, $location, $routeParams, ngToast) {
                var vm = this;
                var id = $routeParams.id;

                DS.find('timeslices', id).then(function (slice) {
                    vm.slice = slice;
                    console.log(slice);
                });

                vm.save = function () {
                    vm.slice.DSSave().then(function () {
                        ngToast.create({
                            className: 'success',
                            content: 'Update Successful'
                        });
                        $location.url('/timesheet/' + vm.slice.project_id);
                    }, function (resp) {
                        ngToast.create({
                            className: 'danger',
                            content: 'Update failed. ' + angular.fromJson(resp.data.errors)
                        });
                    });
                };

                vm.back = function () {
                    $location.url('/timesheet/' + vm.slice.project_id);
                };
            })

            ;
})();


