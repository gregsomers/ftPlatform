angular.module('ftApp')
        .factory('$global', ['DS', '$q', function (DS, $q) {
                var _this = this;
                var _activeTimeSliceId = null;

                //grab the pre-loaded data from the global var               
                DS.inject('settings', window.ftAppInitData['settings']);
                DS.inject('users', window.ftAppInitData['user']);
                if (window.ftAppInitData['activeSlice']) {
                    _this._activeTimeSliceId = window.ftAppInitData['activeSlice'].id;
                    DS.inject('timeslices', window.ftAppInitData['activeSlice']);
                }
                DS.inject('paymentmethods', window.ftAppInitData['paymentMethods']);
                DS.inject('currencies', window.ftAppInitData['currencies']);

                return {
                    getActiveSliceId: function () {
                        return _this._activeTimeSliceId;
                    },
                    getActiveSlice: function () {
                        if (_this._activeTimeSliceId) {
                            return DS.get('timeslices', _this._activeTimeSliceId);
                        }
                        return {};
                    },
                    setActiveSliceId: function (id) {
                        _this._activeTimeSliceId = id;
                        return _this._activeTimeSliceId;
                    },
                    clearActiveSlice: function () {
                        _this._activeTimeSliceId = null;
                    },
                    getAppSetting: function (name) {
                        return DS.get('settings', name);
                    },
                    getCurrentUser: function (name) {
                        return DS.getAll('users')[0];
                    },
                    //helper methods
                    arrayRemoveElm: function (array, elm) {
                        if (Array.isArray(array)) {
                            var idx = array.indexOf(elm);
                            if (idx !== -1) {
                                array.splice(idx, 1);
                            }
                        }
                    }
                };
            }])




        ;
