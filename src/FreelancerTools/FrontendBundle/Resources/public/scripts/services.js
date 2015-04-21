angular.module('ftApp')

        .factory('Client', function ($http) {
            var clients = [];
            var fetchedFromServer = false;
            var self = this;
            var dataDirty = false;

            return {
                loadAll: function () {
                    return $http.get('http://time-dev.gregsomers.com/customers/api');
                },
                get: function () {
                    if (fetchedFromServer === false) {
                        console.log("requesting client data");
                        this.loadAll().
                                success(function (data) {
                                    // this callback will be called asynchronously
                                    // when the response is available
                                    angular.forEach(data, function (value, key) {
                                        clients.push(value);
                                        //console.log("Value %o",value);
                                    });
                                    fetchedFromServer = true;
                                }).
                                error(function (data) {
                                    // called asynchronously if an error occurs
                                    // or server returns response with an error status.
                                });
                    }
                    return clients;
                },
                getById: function (id) {
                    return $http.get('http://time-dev.gregsomers.com/customers/api/' + id);
                },
                save: function () {
                    console.log("saved");
                },
                insert: function (client) {

                    $http.post('http://time-dev.gregsomers.com/customers/api', client);

                    clients.push(client);
                },
                remove: function (id) {
                    $http.delete('http://time-dev.gregsomers.com/customers/api/' + id).success(function () {
                        angular.forEach(clients, function (value, key) {
                            if (value.id == id) {  //cant use === id might not be sent from server as int                 
                                clients.splice(key, 1);
                            }
                        });
                    });
                },
                update: function (id, client) {
                    /*
                     var object = $.grep(clients, function (e) {
                     return e.id == id;
                     })[0];*/

                    angular.forEach(clients, function (value, key) {
                        if (value.id == id) {  //cant use === id might not be sent from server as int                 
                            clients.splice(key, 1);
                            clients.push(client);
                            return $http.post('http://time-dev.gregsomers.com/customers/api/' + client.id, client);
                        }
                    });


                }
            };
        })


        .factory('Project', function ($http) {
            var projects = [];
            var fetchedFromServer = false;
            var self = this;
            var dataDirty = false;

            return {
                loadAll: function () {
                    return $http.get('http://time-dev.gregsomers.com/projects/api');
                },
                get: function () {
                    if (fetchedFromServer === false) {
                        console.log("requesting  data");
                        this.loadAll().
                                success(function (data) {
                                    // this callback will be called asynchronously
                                    // when the response is available
                                    angular.forEach(data, function (value, key) {
                                        projects.push(value);
                                        //console.log("Value %o",value);
                                    });
                                    fetchedFromServer = true;
                                }).
                                error(function (data) {
                                    // called asynchronously if an error occurs
                                    // or server returns response with an error status.
                                });
                    }
                    return projects;
                },
                getById: function(id) {
                    return $http.get('http://time-dev.gregsomers.com/projects/api/' + id);
                }
            }
        })


        ;
