jQuery(document).ready(function() {

jQuery('#aClearAll').click(function(e) {
    e.preventDefault();
    jQuery.confirm({
        title: 'Are you sure?',
        message: '<p style="color: black; max-width: 550px">This will delete all Sharingforce data from the WordPress database (Sharingforce API keys and Widget IDs). The widgets will also be removed from your website. Are you sure you want to continue?</p>',
        buttons: {
            'Yes, Delete': { 'action': onConfirmed, 'focused': true },
            Cancel: {}
        }
    });
    function onConfirmed() {
        location.href = jQuery('#aClearAll').attr('href');
    }
});

jQuery('.button-small').click(function(e) {
    if (jQuery(this).hasClass('disabled')) {
        e.preventDefault();
        return false;
    }
});

jQuery('a[id^=aSelectWidget_]').click(function(e) {
    e.preventDefault();
    var widgetType = this.id.split('_')[1];
    sharingforceSelectWidget(
        widgetType
    );
});

jQuery('#aPerPostWidgetDisabledUrls_Save').click(function() {
    var data = {
        action: 'sharingforce_store_per_post_widget_disabled_urls',
        nonce: sharingforceNonce,
        perPostWidgetDisabledUrls: jQuery('#txtPerPostWidgetDisabledUrls').val()
    };
    messageBoxPleaseWait('Saving...');
    jQuery.post(
        ajaxurl,
        data,
        function (result) {
            if (result['errorHtml']) {
                showQuickMessageError(result['errorHtml']);
            } else {
                showQuickMessage('Disabled pages stored successfully');
            }
            messageBoxClose();
        }
    );
});

jQuery('#aPerPageWidgetDisabledUrls_Save').click(function() {
    var data = {
        action: 'sharingforce_store_per_page_widget_disabled_urls',
        nonce: sharingforceNonce,
        perPageWidgetDisabledUrls: jQuery('#txtPerPageWidgetDisabledUrls').val()
    };
    messageBoxPleaseWait('Saving...');
    jQuery.post(
        ajaxurl,
        data,
        function (result) {
            if (result['errorHtml']) {
                showQuickMessageError(result['errorHtml']);
            } else {
                showQuickMessage('Disabled pages stored successfully');
            }
            messageBoxClose();
        }
    );
});

jQuery('#aDisablePerPostWidget').click(function() {
    jQuery('#divDisablePerPostWidget').slideDown();
    jQuery('#divDisablePerPostWidget_Button').slideDown();
});

jQuery('#aDisablePerPageWidget').click(function() {
    jQuery('#divDisablePerPageWidget').slideDown();
    jQuery('#divDisablePerPageWidget_Button').slideDown();
});

});
