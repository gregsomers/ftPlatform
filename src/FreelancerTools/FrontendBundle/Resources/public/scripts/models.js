
angular.module('ftApp')
        .run(function (DS) {

            DS.defineResource({
                name: 'invoices',
                idAttribute: 'id',
                relations: {
                    belongsTo: {
                        clients: {
                            localField: 'client',
                            localKey: 'client_id'
                        }
                    },
                    hasMany: {
                        invoiceitems: {
                            localField: 'items',
                            foreignKey: 'invoice_id'
                        },
                        payments: {
                            localField: 'payments',
                            foreignKey: 'invoice_id'
                        }
                    }
                },
                methods: {
                    getTotal: function () {
                        var total = 0;
                        if (this.items) {
                            this.items.forEach(function (item) {
                                total += item.quantity * item.price;
                            });
                        }
                        return total;
                    },
                    getBalance: function () {
                        return this.getTotal() - this.getPaid();
                    },
                    getPaid: function () {
                        var total = 0;
                        if (this.payments) {
                            this.payments.forEach(function (payment) {
                                total += payment.amount;
                            });
                        }
                        return total;
                    },
                    getGuestURL: function () {
                        return window.location.origin + '/guest/invoices/' + this.token;
                    }

                },
            });

            DS.defineResource({
                name: 'invoiceitems',
                idAttribute: 'id',
                relations: {
                    belongsTo: {
                        invoices: {
                            localField: 'invoice',
                            localKey: 'invoice_id'
                        }
                    },
                    hasMany: {
                        timeslices: {
                            localField: 'timeslices',
                            foreignKey: 'invoiceItem_id'
                        }
                    }
                },
                methods: {
                    removeTimeslices: function (save) {
                        save = typeof save !== 'undefined' ? save : true; //default value for save
                        if (this.timeslices) {
                            //mark all slices added to this invoice item as not invoiced
                            this.timeslices.forEach(function (slice) {
                                slice.invoiceItem = null;
                                slice.invoiceItem_id = null;
                                slice.invoiced = false;
                                if (save) {
                                    slice.DSSave();
                                }
                            });
                        }
                    }
                }
            });

            DS.defineResource({
                name: 'payments',
                idAttribute: 'id',
                relations: {
                    belongsTo: {
                        invoices: {
                            localField: 'invoice',
                            localKey: 'invoice_id'
                        }
                    }
                }
            });


            DS.defineResource({
                name: 'clients',
                idAttribute: 'id',
                keepChangeHistory: true
            });

            DS.defineResource({
                name: 'timeslices',
                idAttribute: 'id',
                methods: {
                    isBilled: function () {
                        if (this.invoiceItem || this.invoiceItem_id) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                },
                relations: {
                    belongsTo: {
                        activities: {
                            // localField is for linking relations
                            localField: 'activity', // concept.workshop -> reference to this concepts's workshop
                            // localKey is the "join" field
                            localKey: 'activity_id', // the name of the field on an action that points to its parent workshop
                            //parent: true
                        }
                    }
                }
            });

            DS.defineResource({
                name: 'projects',
                idAttribute: 'id',
                methods: {
                    getUnbilledTime: function () {
                        var _this = this;
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            //console.log(slice);
                            if (!slice.isBilled()) {
                                seconds += slice.duration;
                            }
                        });
                        return seconds;
                    },
                    getUnbilledValue: function () {
                        var _this = this;
                        var total = 0;
                        var rate = 0;
                        this.timeslices.forEach(function (slice) {
                            //console.log(slice);
                            if (!slice.isBilled()) {
                                rate = (slice.activity.rate) ? slice.activity.rate : _this.rate;
                                total += slice.duration / 3600 * rate;
                            }
                        });
                        return total;
                        //return this.getUnbilledTime() * this.rate / 3600;
                    },
                    getTotalTime: function () {
                        var _this = this;
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            seconds += slice.duration;
                        });
                        return seconds;
                    },
                    getBilledTime: function () {
                        var _this = this;
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            //console.log(slice);
                            if (slice.isBilled()) {
                                seconds += slice.duration;
                            }
                        });
                        return seconds;
                    },
                },
                relations: {
                    hasMany: {
                        activities: {
                            // localField is for linking relations
                            localField: 'activities', // workshop.concepts -> array of concepts of this workshop
                            // foreignKey is the "join" field
                            foreignKey: 'project_id' // the name of the field on a concept that points to its parent workshop
                        },
                        timeslices: {
                            // localField is for linking relations
                            localField: 'timeslices', // workshop.concepts -> array of concepts of this workshop
                            // foreignKey is the "join" field
                            foreignKey: 'project_id' // the name of the field on a concept that points to its parent workshop
                        }
                    },
                    belongsTo: {
                        clients: {
                            localField: 'client',
                            localKey: 'client_id'
                        }
                    }
                }
            });

            DS.defineResource({
                name: 'activities',
                idAttribute: 'id',
                methods: {
                    getUnbilledTime: function () {
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            //console.log(slice);
                            if (!slice.isBilled()) {
                                seconds += slice.duration;
                            }
                        });
                        return seconds;
                    },
                    getBilledTime: function () {
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            //console.log(slice);
                            if (slice.isBilled()) {
                                seconds += slice.duration;
                            }
                        });
                        return seconds;
                    },
                    getTotalTime: function () {
                        var seconds = 0;
                        this.timeslices.forEach(function (slice) {
                            seconds += slice.duration;
                        });
                        return seconds;
                    },
                    isRunning: function () {
                        var isRunning = false;
                        var sliceRet = null;
                        this.timeslices.forEach(function (slice) {
                            if (!slice.stoppedAt || slice.stoppedAt === null || slice.stoppedAt === "") {
                                isRunning = true;
                                sliceRet = slice;
                            }
                        });
                        return sliceRet;
                    },
                    getRunningTimeslice: function () {
                        var sliceRet = null;
                        this.timeslices.forEach(function (slice) {
                            if (!slice.stoppedAt || slice.stoppedAt === null || slice.stoppedAt === "") {
                                sliceRet = slice;
                            }
                        });
                        return sliceRet;
                    }
                },
                relations: {
                    hasMany: {
                        timeslices: {
                            localField: 'timeslices',
                            foreignKey: 'activity_id',
                            parent: true
                        }
                    },
                    belongsTo: {
                        projects: {
                            // localField is for linking relations
                            localField: 'project', // concept.workshop -> reference to this concepts's workshop
                            // localKey is the "join" field
                            localKey: 'project_id', // the name of the field on an action that points to its parent workshop
                            ///parent: true
                        }
                    }
                }
            });

            DS.defineResource({
                name: 'emailtemplates',
                idAttribute: 'id'
            });

            DS.defineResource({
                name: 'settings',
                idAttribute: 'idString'
            });

            DS.defineResource({
                name: 'users',
                idAttribute: 'id'
            });

            DS.defineResource({
                name: 'paymentmethods',
                idAttribute: 'id'
            });
            
            DS.defineResource({
                name: 'currencies',
                idAttribute: 'id'
            });
            
            




        });


