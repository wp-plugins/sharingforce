// based on colorbox: http://jacklmoore.com/colorbox/

/**
 * @param string content: HTML or #divId
 * @param array options:
 *      title: 'Custom title',
 *      cannotClose: true, // to disable closing
 *      transition: 'elastic', 'fade' or 'none'
 */
function messageBox(content, options) {
    if (!content.length) {
        return;
    }
    if (!options) {
        options = {};
    }
    if (!options['transition']) {
        options['transition'] = 'elastic';
    }
    var colorboxOptions = {
        transition: options['transition'],
        opacity: 0.5,
        fastIframe: 'false',
        initialWidth: 70,
        initialHeight: 30,
    };
    if (options['top']) {
        colorboxOptions['top'] = options['top'];
    }
    if (content[0] == '#') {
        colorboxOptions['inline'] = true;
        colorboxOptions['href'] = content;
    } else {
        colorboxOptions['inline'] = false;
        colorboxOptions['html'] = content;
    }
    if (options['title']) {
        colorboxOptions['title'] = options['title'];
    }
    if (options['cannotClose']) {
        colorboxOptions['close'] = false;
        colorboxOptions['overlayClose'] = false;
        colorboxOptions['escKey'] = false;
    } else {
        colorboxOptions['close'] = true;
        colorboxOptions['overlayClose'] = true;
        colorboxOptions['escKey'] = true;
    }

    jQuery.colorbox(colorboxOptions);
}

function messageBoxIsVisible() {
    return jQuery('#colorbox').css('display') != "none";
}

function messageBoxError(messageHtml, inputId) {
    messageHtml = '<div class="messageBoxError">' + messageHtml + '</div>';
    jQuery.confirm({
        title: 'Error',
        message: messageHtml,
        buttons: {OK: {'focused': true}}
    });
    if (inputId) {
        jQuery(document).bind('cbox_closed', function() {
            jQuery(document).unbind('cbox_closed');
            jQuery('#' + inputId).focus();
        });
    }
    return false;
}

function messageBoxPleaseWait(messageHtml) {
    messageHtml = '<div class="messageBoxPleaseWait">' + messageHtml + '</div>';
    messageBox(messageHtml, { cannotClose: true, transition: 'none' });
}

function messageBoxClose() {
    jQuery(document).unbind('cbox_closed');
    jQuery.colorbox.close();
}