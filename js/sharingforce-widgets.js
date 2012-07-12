var SHARINGFORCE_WIDGET_STYLE_SQUARE_BUTTONS = 1;
var SHARINGFORCE_WIDGET_STYLE_COUNTER_TOP = 2;
var SHARINGFORCE_WIDGET_STYLE_COUNTER_RIGHT = 3;
var SHARINGFORCE_WIDGET_STYLE_DOCKED = 4;

var sharingforceWidgets;
var sharingforceOnWidgetSelected;
var sharingforceWidgetType;
var sharingforceRefreshOnWindowFocus;

jQuery(document).ready(function() {

jQuery('#btnSelectWidget_Ok').click(sharingforceSelectWidget_Ok);
jQuery('#btnSelectWidget_Cancel').click(sharingforceSelectWidget_Cancel);
jQuery('a[id^=aClearWidget_]').click(sharingforceClearWidget_Click);
window.addEventListener('focus', sharingforce_WindowFocus);
jQuery('#aNewWidget').click(sharingforceNewWidget_Click);
jQuery('#aRefresh').click(sharingforceRefresh_Click);

});

function sharingforceSelectWidget(widgetType, onWidgetSelected) {
    sharingforceWidgetType = widgetType;
    sharingforceOnWidgetSelected = onWidgetSelected;
    if (sharingforceWidgets) {
        sharingforceDoSelectWidget();
    } else {
        sharingforceLoadAppList(sharingforceDoSelectWidget);
    }
}

function sharingforceDoSelectWidget() {
    var widgetTypeName, widgetTypeNameForLabel, currentWidgetId;
    switch (sharingforceWidgetType) {
        case 'PerPost':
            widgetTypeName = 'Per-Post';
            widgetTypeNameForLabel = 'fixed';
            currentWidgetId = sharingforcePerPostWidgetId;
            break;
        case 'PerPage':
            widgetTypeName = 'Per-Page';
            widgetTypeNameForLabel = 'docked';
            currentWidgetId = sharingforcePerPageWidgetId;
            break;
    }
    jQuery('#spanSelectWidget_WidgetType').text(widgetTypeNameForLabel);

    jQuery('#tWidgets tr').each(function() {
        if (jQuery(this).attr('id') == 'trNoWidget') {
            return;
        }
        jQuery(this).remove();
    });

    var widgetCount = 0, widget, isDocked;
    for (var i = 0; i < sharingforceWidgets.length; i++ ) {
        widget = sharingforceWidgets[i];
        isDocked = widget.style == SHARINGFORCE_WIDGET_STYLE_DOCKED;
        if (
            isDocked && 'PerPost' == sharingforceWidgetType ||
            !isDocked && 'PerPage' == sharingforceWidgetType
        ) {
            continue;
        }
        jQuery('#tWidgets').append(jQuery(
            '<tr>' +
                '<td class="tdRadio"><input type="radio" name="rbWidget" id="rbWidget_' + widget.id + '" value="' + widget.id + '"></td>' +
                '<td class="tdLabel"><label for="rbWidget_' + widget.id + '"><b>' + widget.name + '</b> (' + widget.businessName + ')</label></td>' +
            '</tr>'
        ));
        widgetCount++;
    }

    jQuery('input[name=rbWidget][value=' + currentWidgetId + ']').attr('checked', true);

    jQuery('#spanSelectWidget_WidgetCount').text(widgetCount);
    if (widgetCount > 1) {
        jQuery('#spanSelectWidget_WidgetCount_Plural').show();
    } else {
        jQuery('#spanSelectWidget_WidgetCount_Plural').hide();
    }
    jQuery('#spanWidgetTypeName').text(widgetTypeName);
    messageBox(
        '#divSelectWidget',
        {
            title: 'Select a ' + widgetTypeName + ' Sharing Widget'
        }
    );
}

function sharingforceSelectWidget_Ok() {
    var widgetId = jQuery('input[name=rbWidget]:checked').attr('id').split('_')[1];
    sharingforceStoreWidget(widgetId, sharingforceWidgetType);
}

function sharingforceStoreWidget(widgetId, widgetType) {
    widgetId = parseInt(widgetId);
    var widgetName = null;
    if (widgetId) {
        var widget = sharingforceWidgetById(widgetId);
        widgetName = widget.name;
    }
    var data = {
        action: 'sharingforce_store_widget',
        nonce: sharingforceNonce,
        widgetType: widgetType,
        widgetId: widgetId,
        widgetName: widgetName
    };
    messageBoxPleaseWait('Storing your selection...');
    jQuery.post(
        ajaxurl,
        data,
        function (result) {
            if (result['errorHtml']) {
                showQuickMessageError(result['errorHtml']);
            } else {
                var widgetTypeName;
                if (widgetType == 'PerPost') {
                    sharingforcePerPostWidgetId = widgetId;
                    widgetTypeName = 'Per-Post';
                } else {
                    sharingforcePerPageWidgetId = widgetId;
                    widgetTypeName = 'Per-Page';
                }
                if (widgetId) {
                    showQuickMessage('Selected widget stored successfully');
                    jQuery('#divWidget_' + widgetType)
                        .text(widgetName)
                        .addClass('selected');
                    jQuery('#aEditWidget_' + widgetType)
                        .attr('href', sharingforceUrl_WidgetEdit(widgetId))
                        .removeClass('disabled');
                    jQuery('#aClearWidget_' + widgetType)
                        .removeClass('disabled');
                } else {
                    showQuickMessage(widgetTypeName + ' widget cleared out');
                    jQuery('#divWidget_' + widgetType)
                        .text('(not selected)')
                        .removeClass('selected');
                    jQuery('#aEditWidget_' + widgetType)
                        .addClass('disabled');
                    jQuery('#aClearWidget_' + widgetType)
                        .addClass('disabled');
                }
            }
            sharingforceWidgetSelectCloseDialog();
        }
    );
}

function sharingforceWidgetById(widgetId) {
    for (var i = 0; i < sharingforceWidgets.length; i++ ) {
        if (sharingforceWidgets[i].id == widgetId) {
            return sharingforceWidgets[i];
        }
    }
    return false;
}

function sharingforceLoadAppList(onSuccess, message) {
    var isCustomMessage = !!message;
    if (!isCustomMessage) {
        message = 'Connecting to Sharingforce&trade;...';
    }
    messageBoxPleaseWait(message);
    sharingforceRequest(
        'appList',
        {
            publicKey: sharingforceApiPublicKey,
            secretKey: sharingforceApiSecretKey,
            request: {
                recordStatus: SHARINGFORCE_RECORD_STATUS_ACTIVE
            }
        },
        function(result) {
            if (!isCustomMessage) {
                showQuickMessage('Connected successfully!');
            }
            sharingforceWidgets = [];
            var items = result.list[0].item;
            if (items) {
                for (var i = 0; i < items.length; i++ ) {
                    var recordStatus = parseInt(items[i].recordStatus[0].Text);
                    if (recordStatus != SHARINGFORCE_RECORD_STATUS_ACTIVE) {
                        continue;
                    }
                    sharingforceWidgets.push({
                        id: parseInt(items[i].id[0].Text),
                        name: items[i].name[0].Text,
                        style: parseInt(items[i].style[0].Text),
                        businessName: items[i].businessName[0].Text
                    });
                }
            }
            onSuccess();
        },
        function(errorMessage, errorCode) {
            messageBoxError(errorMessage);
        }
    );
}

function sharingforceClearWidget_Click(e) {
    e.preventDefault();
    if (jQuery(this).hasClass('disabled')) {
        return false;
    }
    var widgetType = this.id.split('_')[1];
    sharingforceStoreWidget(0, widgetType);
}

function sharingforce_WindowFocus() {
    if (sharingforceRefreshOnWindowFocus) {
        sharingforceRefreshOnWindowFocus = false;
        sharingforceRefreshWidgets();
    }
}

function sharingforceNewWidget_Click() {
    sharingforceRefreshOnWindowFocus = true;
}

function sharingforceRefresh_Click(e) {
    e.preventDefault();
    sharingforceRefreshWidgets();
}

function sharingforceRefreshWidgets() {
    sharingforceLoadAppList(function() {
        sharingforceSelectWidget(
            sharingforceWidgetType,
            sharingforceOnWidgetSelected
        );
    }, 'Reloading widgets...');
}

function sharingforceWidgetSelectCloseDialog() {
    if (sharingforceOnWidgetSelected) {
        sharingforceOnWidgetSelected();
    } else {
        messageBoxClose();
    }
}

function sharingforceSelectWidget_Cancel() {
    sharingforceWidgetSelectCloseDialog();
}