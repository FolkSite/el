el.Login = {
    baseParams: {
        action: '',
        namespace: eccConfig.el.namespace,
        path: eccConfig.el.path,
        location: 1
    },
    initialize: function() {
        if (!!!el.Init) {
            el.initialize();
        }
        $(document).on('submit', '.el-operation', function(e) {
            e.preventDefault();
            return false;
        });
        $(document).on('click', 'form.el-login [type="button"]', function(e) {
            var $this = $(this);
            var confirm = $this.data('confirm');
            el.Login.action($this.closest('form.el-login'), $this, confirm);
            e.preventDefault();
            return false;
        });

        $(document).on('click', 'form.el-logout [type="button"]', function(e) {
            var $this = $(this);
            var confirm = $this.data('confirm');
            el.Login.action($this.closest('form.el-logout'), $this, confirm);
            e.preventDefault();
            return false;
        });

        $(document).ready(function () {

        });

    },

    action: function(form, button, confirm) {
        if (confirm) {
            el.Login.Сonfirm(form, button);
            return false;
        }
        var action = $(button).prop('name');

        $(form).ajaxSubmit({
            data: $.extend({},
                el.Login.baseParams, {
                    action: action
                }),
            url: eccConfig.actionUrl,
            form: form,
            button: button,
            dataType: 'json',
            beforeSubmit: function() {
                $(button).attr('disabled', true);
                return true;
            },
            success: function(response) {

                if (response.success) {
                    el.Message.success('', response.message);

                    if (response.object && response.object['process']) {
                        var process = response.object['process'];
                        if (process.id && process.type && process.output != '') {
                            var view = $(elConfig.defaults.selector.view).parent().find('[data-type="' + process.type + '"][data-id="' + process.id + '"]');
                            if (view.length) {
                                view.parent().replaceWith(process.output);
                            }
                        }
                    }

                    if (response.object && response.object['properties'] && response.object['properties']['link_login'] != '') {
                        $.get(response.object['properties']['link_login']);
                    }

                } else {
                    if (response.data && response.data.length > 0) {
                        var errors = [];
                        var i, field;
                        for (i in response.data) {
                            field = response.data[i];
                            var elem = $(form).find('[name="' + field.id + '"]').parent().find('.error');
                            if (elem.length > 0) {
                                elem.text(field.msg)
                            }
                            else if (field.id && field.msg) {
                                errors.push(field.id + ': ' + field.msg);
                            }
                        }
                        if (errors.length > 0) {
                            el.Message.error('', errors.join('<br/>'));
                        }
                    }
                    else {
                        el.Message.error('', response.message);
                    }
                }
                $(button).attr('disabled', false);
            },

            error: function(response) {
                $(button).attr('disabled', false);
            }

        });
    }

};

el.Login.Сonfirm = function(form, button) {
    var type = $(button).data('type');
    var message = $(button).data('message');
    el.Confirm.form(form, type, '', message).get()
        .on('pnotify.confirm', function() {
            el.Login.action(form, button, false);
            return true;
        })
        .on('pnotify.cancel', function() {
            return true;
        });
};

el.Login.initialize();