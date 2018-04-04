// MAIN APP CONTROLLER
( function($) {
    $.ajaxSetup({
        cache: false
    });

    // Set up CSRF
    $(document).ajaxSend(function(event, xhr, settings) {
        if (settings.type != 'POST') {
            return;
        }
        var matches = document.cookie.match(new RegExp("(?:^|; )_csrf=([^;]*)"));
        var csrf = matches ? decodeURIComponent(matches[1]) : '';
        if (!settings.data && settings.data !== 0) {
            settings.data = '';
        }
        if (typeof(settings.data) === "string") {
            if (settings.data.indexOf('_csrf=') == -1) {
                settings.data += (settings.data.length > 0 ? '&' : '') + '_csrf=' + csrf;
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            }
        } else if (typeof(settings.data) === "object") {
            if (window.FormData && settings.data instanceof window.FormData) {
                if ('function' == typeof settings.data.set) {
                    settings.data.set('_csrf', csrf);
                } else {
                    settings.data.append('_csrf', csrf);
                }
            } else {
                settings.data['_csrf'] = csrf;
            }
        }
    });

    $.telegram = $.extend($.telegram || {}, {
        lang: false,
        app_url: false,
        backend_url: false,
        is_debug: false,
        content: false,
        sidebar: false,
        storage: new $.store(),
        title: {
            pattern: "Telegram — %s",
            set: function( title_string ) {
                if (title_string) {
                    var state = history.state;
                    if (state) {
                        state.title = title_string;
                    }
                    document.title = $.telegram.title.pattern.replace("%s", title_string);
                }
            }
        },

	});

})(jQuery);