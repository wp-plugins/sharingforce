var SHARINGFORCE_RECORD_STATUS_ACTIVE = 1;
var SHARINGFORCE_RECORD_STATUS_DEACTIVATED = 2;

function sharingforceRequest(
    requestName,
    params,
    successCallback,
    errorCallback
) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            if (xmlhttp.status == 200) {
                // http://www.terracoder.com convert XML to JSON
                var json = jQuery.xmlToJSON(xmlhttp.responseXML);
                var result = eval('json.Body[0].' + requestName + 'Response[0].return[0]');
                successCallback(result);
            } else {
                if (errorCallback) {
                    var unknownError = false;
                    if (xmlhttp.responseXML.childNodes.length != 1) {
                        unknownError = true;
                    }
                    if (!unknownError) {
                        var envelope = xmlhttp.responseXML.childNodes[0];
                        if (envelope.localName != 'Envelope') {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        if (envelope.childNodes.length != 1) {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        var body = envelope.childNodes[0];
                        if (body.localName != 'Body') {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        if (body.childNodes.length != 1) {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        var fault = body.childNodes[0];
                        if (fault.localName != 'Fault') {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        if (fault.childNodes.length != 2) {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        var faultcode = fault.childNodes[0];
                        if (faultcode.localName != 'faultcode') {
                            unknownError = true;
                        }
                    }
                    if (!unknownError) {
                        var faultstring = fault.childNodes[1];
                        if (faultstring.localName != 'faultstring') {
                            unknownError = true;
                        }
                    }
                    var errorMessage, errorCode;
                    if (!unknownError) {
                        errorMessage = faultstring.textContent;
                        errorCode = faultcode.textContent;
                    } else {
                        errorMessage = 'There was a problem connecting to Sharingforce server, please try again later.';
                        errorCode = 500;
                    }
                    errorCallback(errorMessage, errorCode);
                }
            }
        }
    }
    xmlhttp.open('POST', 'https://www.sharingforce.com/api/', true);
    xmlhttp.setRequestHeader('SOAPAction', 'https://www.sharingforce.com/api/');
    xmlhttp.setRequestHeader('Content-Type', 'text/xml; charset=utf-8');
    var xml = '<?xml version="1.0" encoding="utf-8"?>' +
        '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' +
            'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ' +
            'xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' +
        '<soap:Body>' +
            '<' + requestName + ' xmlns="https://www.sharingforce.com/api/">';
    for (var paramName in params) {
        xml += '<' + paramName + '>';
        var param = params[paramName];
        if (typeof param == 'object') {
            for (var propertyName in param) {
                xml += '<' + propertyName + '>' + param[propertyName] + '</' +
                    propertyName + '>';
            }
        } else {
            xml += param;
        }
        xml += '</' + paramName + '>';
    }
    xml +=
            '</' + requestName + '>' +
        '</soap:Body>' +
        '</soap:Envelope>';
    xmlhttp.send(xml);
    return xmlhttp;
}