// Example:
//    $.confirm({
//        title: 'New API key',
//        message: '<p>After generating new API key, you won\'t be able to restore the current key at a later time! Continue?</p>',
//        buttons: {
//            Yes: { 'action': onBusinessNewApiKey_Yes, 'focused': true },
//            Cancel: {}
//        }
//    });
(function($){
    $.confirm = function(params) {
        if ($('#confirm-box').length) {
            // A confirm is already shown on the page:
            return;
        }

        var markup = [
            '<div style="display: none;"><div id="confirm-box">',
            params.message,
            '<div id="confirm-buttons"></div></div></div>'
        ].join('');

        var focusedButtonId = null;

        $(document.body).append(markup);

        var buttonNumber = 0;
        $.each(params.buttons, function(name, obj) {
            buttonId = 'b' + buttonNumber.toString();
            buttonNumber++;

            // Generating the markup for the buttons:
            buttonHTML = $('<a class="button-small">' + name + '</a>');
            $(buttonHTML).attr('href', (obj.href ? obj.href : '#'));
            $(buttonHTML).attr('id', buttonId);

            $('#confirm-buttons').append(buttonHTML);

            if (!obj.action) {
                obj.action = function(){ return true; };
            }

            if (obj.focused) {
                focusedButtonId = buttonId;
            }

            $(buttonHTML).click(function(e) {
                var isLink = !!obj.href;
                if (!isLink) {
                    e.preventDefault();
                }
                obj.action();
                jQuery.colorbox.close();
                jQuery('#confirm-box').remove();
                return isLink;
            });
        });

        var colorboxOptions = {
            transition: 'elastic', // 'elastic', 'fade' or 'none'
            opacity: 0.5,
            fastIframe: 'false',
            initialWidth: 70,
            initialHeight: 30,
            inline: true,
            href: '#confirm-box',
            title: params.title,
            close: true,
            overlayClose: false,
            onClosed: function(){ $('#confirm-box').remove(); },
            escKey: false
        };

        $.colorbox(colorboxOptions);

        if (focusedButtonId) {
            setTimeout(function() {
                $('#confirm-box #confirm-buttons #' + focusedButtonId).focus();
            }, 300);
        }
    }
})(jQuery);
