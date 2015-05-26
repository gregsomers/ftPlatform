(function () {
    'use strict';

    angular.module('ftApp')

            .filter('active', function () {
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
            })

            .filter('archived', function () {
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
            })

            .filter('secondsToTimeFormat', function myDateFormat($filter) {
                return function (text) {
                    if (text === 0) {
                        return "00:00";
                    }

                    var duration = moment.duration(text, 'seconds');
                    return duration.format("h:mm:ss", {trim: false});
                };
            })

            .filter('secondsToDateFormat', function myDateFormat($filter) {
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
            })

            .filter('myDateFormat', function myDateFormat($filter) {
                return function (text) {
                    return moment(text).format("DD-MMM-YYYY");
                };
            })





            ;
})();
