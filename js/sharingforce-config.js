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

//jQuery('#frmEnableSharingforce').submit(function(e) {
//    e.preventDefault();
//    var errorMsg, errorInputId;
//
//    if (
//        !validateString('firstName', 'First Name', 0, 50) ||
//        !validateString('lastName', 'Last Name', 0, 50) ||
//        !validateEmail('email') ||
//        !validateString('websiteUrl', 'Website URL', 4, 255) ||
//        !validateString('businessName', 'Business / Blog Name', 3, 50) ||
//        !validateString('password', 'Your Sharingforce&trade; password', 6, 32)
//    ) {
//        return false;
//    }
//    if (jQuery('#password').val() != jQuery('#passwordConfirm').val()) {
//        return messageBoxError('Passwords do not match. Please make sure you enter the same password in both "Password" and "Confirm Password" fields.', 'passwordConfirm');
//    }
//    if (!jQuery('#chbAgreeToTerms').is(':checked')) {
//        return messageBoxError(
//            'Please agree to Sharingforce Terms of Use.',
//            'chbAgreeToTerms'
//        );
//    }
//    if (!jQuery('#chbAgreeToPoweredBySharingforceLink').is(':checked')) {
//        return messageBoxError(
//            'Please grant us a permission to display "Powered by Sharingforce" link in the widget.',
//            'chbAgreeToPoweredBySharingforceLink'
//        );
//    }
//
//    messageBoxPleaseWait('Registering with Sharingforce&trade;...');
//    sharingforceRequest(
//        'registerWordPressBlog',
//        {
//            request: {
//                firstName: jQuery('#firstName').val(),
//                lastName: jQuery('#lastName').val(),
//                email: jQuery('#email').val(),
//                websiteUrl: jQuery('#websiteUrl').val(),
//                businessName: jQuery('#businessName').val(),
//                password: jQuery('#password').val()
//            }
//        },
//        function(result) {
//            storeSharingforceData(
//                result.publicApiKey[0].Text,
//                result.secretApiKey[0].Text,
//                result.perPageWidgetId[0].Text,
//                result.perPageWidgetName[0].Text,
//                result.perPostWidgetId[0].Text,
//                result.perPostWidgetName[0].Text
//            );
//            showQuickMessage('Registration successful');
//        },
//        function(errorMessage, errorCode) {
//            messageBoxError(errorMessage);
//        }
//    );
//});

function storeSharingforceData(
    publicApiKey,
    secretApiKey,
    perPageWidgetId,
    perPageWidgetName,
    perPostWidgetId,
    perPostWidgetName
) {
    var data = {
        action: 'sharingforce_store_sharingforce_data',
        nonce: sharingforceNonce,
        publicApiKey: publicApiKey,
        secretApiKey: secretApiKey,
        perPageWidgetId: perPageWidgetId,
        perPageWidgetName: perPageWidgetName,
        perPostWidgetId: perPostWidgetId,
        perPostWidgetName: perPostWidgetName
    };
    jQuery.post(
        ajaxurl,
        data,
        function (result) {
            if (result['errorHtml']) {
                showQuickMessageError(result['errorHtml']);
            } else {
                jQuery.confirm({
                    title: 'Sharingforce&trade; Enabled',
                    message: '<p style="color: black; max-width: 550px">Congratulations! We have created a Sharingforce&trade; account for you, and connected your WordPress installation to it. Thank you for using Sharingforce&trade;!</p>',
                    buttons: {
                        'Continue &raquo;': {
                            'action': function() {
                                location.href = sharingforceConfigUrl;
                            },
                            'focused': true
                        }
                    }
                });
            }
        }
    );
}


});
