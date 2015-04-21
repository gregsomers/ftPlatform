angular.module('ftApp')
        .controller('ClientListCtrl', function ($scope, Client, $location) {
            $scope.clients = Client.get();
            $scope.fields = ['name'];

            $scope.sort = function (field) {
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
            };

            $scope.sort.field = "name";
            $scope.sort.order = false;

            $scope.edit = function (id) {
                $location.url('/client/' + id);

                console.log("navigate to edit " + id);
            };
            $scope.remove = function (id) {
                Client.remove(id);
            };


            $scope.create = function () {
                $location.url('/client/new');
            };


        })

        .controller('TimesheetListCtrl', function ($scope, Project, $location) {
            $scope.projects = Project.get();
            $scope.fields = ['name', 'customer.name'];

            $scope.sort = function (field) {
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
            };

            $scope.sort.field = "name";
            $scope.sort.order = false;

            $scope.show = function (id) {
                $location.url('/timesheet/' + id);
            };
        })

        .controller('TimesheetShowCtrl', function ($scope, Project, $location, $routeParams) {

            $scope.id = $routeParams.id;
            $scope.project = {};
            $scope.slices = {};
            $scope.activities = {};

            Project.getById($scope.id).success(function (data) {
                $scope.project = data.project;
                $scope.slices = data.slices;
                $scope.activities = data.activities;
                console.log(data);
            });

            $scope.fields = ['slice.startedAt', 'slice.activity', 'slice.time'];

            $scope.sort = function (field) {
                $scope.sort.field = field;
                $scope.sort.order = !$scope.sort.order;
            };

            $scope.sort.field = "slice.startedAt";
            $scope.sort.order = false;

            $scope.tabs = [
                {title: 'Timeslices', content: 'Dynamic content 1'},
                {title: 'Activities', content: 'Dynamic content 2', disabled: true}
            ];

        })

        .controller('ClientNewCtrl', function ($scope, Client, $location) {
            $scope.client = {};

            $scope.save = function () {

                //
                console.log("%o", $scope.client);

                if ($scope.client.$invalid) {

                } else {
                    Client.insert($scope.client);
                    $location.url('/clients');
                }

            };
        })

        .controller('ClientEditCtrl', function ($scope, Client, $location, $routeParams) {

            $scope.id = $routeParams.id;
            $scope.client = {};

            Client.getById($scope.id).success(function (data) {
                $scope.client = data;
            });


            $scope.save = function () {

                console.log("%o", $scope.client);

                Client.update($scope.id, $scope.client);

                if ($scope.client.$invalid) {

                } else {
                    //Client.insert($scope.client);

                    $location.url('/clients');
                }

            };
        })


        ;


