(function ($) {

    Craft.WheelformExport = Garnish.Base.extend(
        {
            $trigger: null,
            $form: null,

            init: function (formId) {
                this.$form = $('#' + formId);
                this.$trigger = $('input.submit', this.$form);
                this.$status = $('.utility-status', this.$form);

                this.addListener(this.$form, 'submit', 'onSubmit');
            },

            onSubmit: function (ev) {
                ev.preventDefault();

                if (!this.$trigger.hasClass('disabled')) {
                    if (!this.progressBar) {
                        this.progressBar = new Craft.ProgressBar(this.$status);
                    }
                    else {
                        this.progressBar.resetProgressBar();
                    }

                    this.progressBar.$progressBar.removeClass('hidden');

                    this.progressBar.$progressBar.velocity('stop').velocity(
                        {
                            opacity: 1
                        },
                        {
                            complete: $.proxy(function () {
                                var postData = Garnish.getPostData(this.$form),
                                    params = Craft.expandPostArray(postData);

                                var data = {
                                    params: params
                                };

                                Craft.postActionRequest(params.action, data, $.proxy(function (response, textStatus) {
                                    if (textStatus === 'success') {
                                        if (response && response.error) {
                                            alert(response.error);
                                        }

                                        this.updateProgressBar();

                                        if (response && response.csvFile) {
                                            var $iframe = $('<iframe/>', {
                                                'src': Craft.getActionUrl('wheelform/entries/download-file',
                                                { 'filename': response.csvFile })
                                            }).hide();
                                            this.$form.append($iframe);
                                        }

                                        setTimeout($.proxy(this, 'onComplete'), 300);
                                    }
                                    else {
                                        Craft.cp.displayError(Craft.t('wheelform', 'There was a problem exporting your form entries.'));

                                        this.onComplete(false);
                                    }

                                }, this),
                                    {
                                        complete: $.noop
                                    });

                            }, this)
                        });

                    if (this.$allDone) {
                        this.$allDone.css('opacity', 0);
                    }

                    this.$trigger.addClass('disabled');
                    this.$trigger.trigger('blur');
                }
            },

            updateProgressBar: function () {
                var width = 100;
                this.progressBar.setProgressPercentage(width);
            },

            onComplete: function (showAllDone) {

                if (!this.$allDone) {
                    this.$allDone = $('<div class="alldone" data-icon="done" />').appendTo(this.$status);
                    this.$allDone.css('opacity', 0);
                }

                this.progressBar.$progressBar.velocity({ opacity: 0 }, {
                    duration: 'fast', complete: $.proxy(function () {
                        if (typeof showAllDone === 'undefined' || showAllDone === true) {
                            this.$allDone.velocity({ opacity: 1 }, { duration: 'fast' });
                        }

                        this.$trigger.removeClass('disabled');
                        this.$trigger.trigger('focus');
                    }, this)
                });
            }
        });

    Craft.WheelformExportFields = Garnish.Base.extend(
        {
            $trigger: null,
            $form: null,

            init: function (formId) {
                this.$form = $('#' + formId);
                this.$trigger = $('input.submit', this.$form);
                this.$status = $('.utility-status', this.$form);

                this.addListener(this.$form, 'submit', 'onSubmit');
            },

            onSubmit: function (ev) {
                ev.preventDefault();

                if (!this.$trigger.hasClass('disabled')) {
                    if (!this.progressBar) {
                        this.progressBar = new Craft.ProgressBar(this.$status);
                    }
                    else {
                        this.progressBar.resetProgressBar();
                    }

                    this.progressBar.$progressBar.removeClass('hidden');

                    this.progressBar.$progressBar.velocity('stop').velocity(
                        {
                            opacity: 1
                        },
                        {
                            complete: $.proxy(function () {
                                var postData = Garnish.getPostData(this.$form),
                                    params = Craft.expandPostArray(postData);

                                var data = {
                                    params: params
                                };

                                Craft.postActionRequest(params.action, data, $.proxy(function (response, textStatus) {
                                    if (textStatus === 'success') {
                                        if (response && response.error) {
                                            alert(response.error);
                                        }

                                        this.updateProgressBar();

                                        if (response && response.jsonFile) {
                                            var $iframe = $('<iframe/>', {
                                                'src': Craft.getActionUrl('wheelform/form/download-file',
                                                    { 'filename': response.jsonFile })
                                            }).hide();
                                            this.$form.append($iframe);
                                        }

                                        setTimeout($.proxy(this, 'onComplete'), 300);
                                    }
                                    else {
                                        Craft.cp.displayError(Craft.t('wheelform', 'There was a problem exporting your form fields.'));

                                        this.onComplete(false);
                                    }

                                }, this),
                                    {
                                        complete: $.noop
                                    });

                            }, this)
                        });

                    if (this.$allDone) {
                        this.$allDone.css('opacity', 0);
                    }

                    this.$trigger.addClass('disabled');
                    this.$trigger.trigger('blur');
                }
            },

            updateProgressBar: function () {
                var width = 100;
                this.progressBar.setProgressPercentage(width);
            },

            onComplete: function (showAllDone) {

                if (!this.$allDone) {
                    this.$allDone = $('<div class="alldone" data-icon="done" />').appendTo(this.$status);
                    this.$allDone.css('opacity', 0);
                }

                this.progressBar.$progressBar.velocity({ opacity: 0 }, {
                    duration: 'fast', complete: $.proxy(function () {
                        if (typeof showAllDone === 'undefined' || showAllDone === true) {
                            this.$allDone.velocity({ opacity: 1 }, { duration: 'fast' });
                        }

                        this.$trigger.removeClass('disabled');
                        this.$trigger.trigger('focus');
                    }, this)
                });
            }
        });

    Craft.WheelformImportFields = Garnish.Base.extend(
    {
        $trigger: null,
        $form: null,

        init: function (formId) {
            this.$form = $('#' + formId);
            this.$trigger = $('input.submit', this.$form);
            this.$status = $('.utility-status', this.$form);

            this.addListener(this.$form, 'submit', 'onSubmit');
        },

        onSubmit: function (ev) {
            ev.preventDefault();

            var data = new FormData(this.$form[0]);

            if (!this.$trigger.hasClass('disabled')) {
                if (!this.progressBar) {
                    this.progressBar = new Craft.ProgressBar(this.$status);
                }
                else {
                    this.progressBar.resetProgressBar();
                }

                this.progressBar.$progressBar.removeClass('hidden');

                this.progressBar.$progressBar.velocity('stop').velocity(
                    {
                        opacity: 1
                    },
                    {
                        complete: $.proxy(function () {

                            var headers = {};
                            if (Craft.csrfTokenValue && Craft.csrfTokenName) {
                                headers['X-CSRF-Token'] = Craft.csrfTokenValue;
                            }

                            $.ajax({
                                url: Craft.getActionUrl(this.$form.action),
                                type: 'POST',
                                method: 'POST',
                                dataType: 'json',
                                headers: headers,
                                cache:false,
                                contentType: false,
                                processData: false,
                                data: data,
                                success: $.proxy(function (res) {
                                    if (res.success) {
                                        Craft.cp.displayNotice(Craft.t('wheelform', 'Fields Saved'));

                                        this.updateProgressBar();

                                        this.onComplete(true);

                                    } else {
                                        if (res && res.errors) {
                                            for (var i = 0; i < res.errors.length; i++) {
                                                var error = res.errors[i];
                                                Craft.cp.displayError(error);
                                            }
                                        }

                                        this.onComplete(false);
                                    }

                                }, this)
                            });
                        }, this)
                    });

                if (this.$allDone) {
                    this.$allDone.css('opacity', 0);
                }

                this.$trigger.addClass('disabled');
                this.$trigger.trigger('blur');
            }
        },

        updateProgressBar: function () {
            var width = 100;
            this.progressBar.setProgressPercentage(width);
        },

        onComplete: function (showAllDone) {

            if (!this.$allDone) {
                this.$allDone = $('<div class="alldone" data-icon="done" />').appendTo(this.$status);
                this.$allDone.css('opacity', 0);
            }

            this.progressBar.$progressBar.velocity({ opacity: 0 }, {
                duration: 'fast', complete: $.proxy(function () {
                    if (typeof showAllDone === 'undefined' || showAllDone === true) {
                        this.$allDone.velocity({ opacity: 1 }, { duration: 'fast' });
                    }

                    this.$trigger.removeClass('disabled');
                    this.$trigger.trigger('focus');
                }, this)
            });
        }
    });

})(jQuery);
