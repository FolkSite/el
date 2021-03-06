if (typeof(el) == 'undefined') {
    el = {
        Init: false
    };
}

el = {
    initialize: function() {
        if (!jQuery().ajaxForm) {
            document.write('<script src="' + eccConfig.assetsUrl + 'vendor/ajaxform/jquery.form.min.js"><\/script>');
        }

        if (!jQuery().pnotify) {
            $('<link/>', {
                rel: 'stylesheet',
                type: 'text/css',
                href: elConfig.assetsBaseUrl + 'components/modpnotify/build/pnotify.custom.css'
            }).appendTo('head');
            document.write('<script src="' + elConfig.assetsBaseUrl + 'components/modpnotify/build/pnotify.custom.js"><\/script>');
        }
        $(document).ready(function() {
            PNotify.prototype.options.styling = "bootstrap3";

        });
        el.Init = true;
    }
};


el.Message = {
    defaults: {
        delay: 4000,
        addclass: 'el-message'
    },
    success: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'success';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.message.title.success : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    error: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'error';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.message.title.error : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    info: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'info';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.message.title.info : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    remove: function() {
        PNotify.removeAll();
    }
};

el.Confirm = {
    defaults: {
        hide: false,
        addclass: 'el-сonfirm',
        icon: 'glyphicon glyphicon-question-sign',
        confirm: {
            confirm: true,
            buttons: [{
                text: elConfig.defaults.yes,
                addClass: 'btn-primary'

            }, {
                text: elConfig.defaults.no,
                addClass: 'btn-danger'

            }]
        },
        buttons: {
            closer: false,
            sticker: false
        },
        history: {
            history: false
        }
    },
    success: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'success';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.confirm.title.success : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    error: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'error';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.confirm.title.error : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    info: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'info';
        notify.text = message;
        notify.title = (!title) ? elConfig.defaults.confirm.title.info : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    form: function(form, type, title, message) {
        if (!type) return false;
        if (form) {
            $.extend(this.defaults, {
                before_init: function(opts) {
                    $(form).find('input[type="button"], button, a').attr('disabled', true);
                },
                after_close: function(PNotify, timer_hide) {
                    $(form).find('input[type="button"], button, a').attr('disabled', false);
                }
            });
        }

        switch (type) {
            case 'success':
                return this.success(title, message);
            default:
            case 'error':
                return this.error(title, message);
            case 'info':
                return this.info(title, message);
        }
    },
    remove: function() {
        return PNotify.removeAll();
    }
};


el.Util = {

    Format: {
        date: function(date) {
            date = new Date(date.getTime());
            date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
            return date.toISOString().replace(/\..*$/, '');
        }
    }
};

