jQuery(document).ready(function() {

jQuery('#frmConnect').submit(function() {
    var errorMsg, errorInputId;
    sharingforceApiPublicKey = jQuery('#publicKey').val();
    sharingforceApiSecretKey = jQuery('#secretKey').val();

    if (!sharingforceApiPublicKey.length) {
        errorMsg = 'Please enter your Sharingforce API public key';
        errorInputId = 'sharingforceApiPublicKey';
    }
    if (!errorMsg) {
        if (sharingforceApiPublicKey.length < 8) {
            errorMsg = 'The value you entered doesn\'t look like a valid Sharingforce API public key. Please copy your keys from Sharingforce API page on sharingforce website (click the link above the form).';
            errorInputId = 'sharingforceApiPublicKey';
        }
    }
    if (!errorMsg) {
        if (!sharingforceApiSecretKey.length) {
            errorMsg = 'Please enter your Sharingforce API secret key';
            errorInputId = 'sharingforceApiSecretKey';
        }
    }
    if (!errorMsg) {
        if (sharingforceApiSecretKey.length != 64) {
            errorMsg = 'The value you entered doesn\'t look like a valid Sharingforce API secret key. Please copy your keys from Sharingforce API page on sharingforce website (click the link above the form).';
            errorInputId = 'sharingforceApiSecretKey';
        }
    }
    if (!errorMsg) {
        if (!jQuery('#chbAgreeToTerms').is(':checked')) {
            errorMsg = 'Please agree to Sharingforce Terms of Use.';
            errorInputId = 'chbAgreeToTerms';
        }
    }
    if (!errorMsg) {
        if (!jQuery('#chbAgreeToPoweredBySharingforceLink').is(':checked')) {
            errorMsg = 'Please grant us a permission to display "Powered by Sharingforce" link in the widget.';
            errorInputId = 'chbAgreeToPoweredBySharingforceLink';
        }
    }
    if (errorMsg) {
        jQuery('#' + errorInputId).focus();
        messageBoxError(errorMsg);
        return false;
    }

    sharingforceLoadAppList(function() {
        storeApiKeys();
        sharingforceSelectWidget(
            'PerPage',
            function() {
                sharingforceSelectWidget(
                    'PerPost',
                    function() {
                        jQuery.confirm({
                            title: 'Connected to Sharingforce&trade;',
                            message: '<p style="color: black; max-width: 550px">Congratulations, you have connected your WordPress installation to your Sharingforce&trade; account! You can always change your widget options on Sharingforce&trade; Configuration page. Thank you for using Sharingforce&trade;!</p>',
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
                );
            }
        );
    });

    return false;
});

function storeApiKeys() {
    var data = {
        action: 'sharingforce_store_api_keys',
        nonce: sharingforceNonce,
        sharingforceApiPublicKey: sharingforceApiPublicKey,
        sharingforceApiSecretKey: sharingforceApiSecretKey
    };
    jQuery.post(
        ajaxurl,
        data,
        function (result) {
            if (result['errorHtml']) {
                showQuickMessageError(result['errorHtml']);
            } else {
                showQuickMessage('API keys stored successfully');
            }
        }
    );
}

});