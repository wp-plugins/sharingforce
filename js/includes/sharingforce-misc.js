function getPageSize() {
    var scrollLeft, scrollTop, innerWidth, innerHeight;
    if (typeof(window.pageYOffset) == 'number') {
        scrollLeft = window.pageXOffset;
        scrollTop = window.pageYOffset;
    } else if(
        document.documentElement &&
        typeof(document.documentElement.scrollLeft) == 'number'
    ) {
        scrollLeft = document.documentElement.scrollLeft;
        scrollTop = document.documentElement.scrollTop;
    } else if(
        document.body &&
        typeof(document.body.scrollLeft) == 'number'
    ) {
        scrollLeft = document.body.scrollLeft;
        scrollTop = document.body.scrollTop;
    }
    if (window.innerHeight) {
        innerHeight = window.innerHeight;
    } else if(document.documentElement.offsetHeight) {
        innerHeight = document.documentElement.offsetHeight;
    }
    if (document.documentElement.offsetWidth) {
        innerWidth = document.documentElement.offsetWidth;
    } else if(window.innerWidth) {
        innerWidth = window.innerWidth;
    }
    return {
        scrollLeft: 0 + scrollLeft,
        scrollTop: 0 + scrollTop,
        width: innerWidth,
        height: innerHeight
    };
}

function setOnWindowResize(eventListener) {
    if (typeof(window.addEventListener) != 'undefined') {
        window.addEventListener('resize', eventListener, false);
    } else if (typeof(document.addEventListener) != 'undefined') {
        document.addEventListener('resize', eventListener, false);
    } else if (typeof window.attachEvent != 'undefined') {
        window.attachEvent('onresize', eventListener);
    }
}

// If maxLen<0 then str is nullable
function isValidString(str, displayLabel, minLen, maxLen) {
    str = '' + str;
    if (minLen > 0 && str == '') {
        return 'Specify '+displayLabel+', please.';
    } else if (maxLen > 0 && str.length > maxLen) {
        return displayLabel + ' should be up to ' + maxLen + ' characters long (you entered ' + str.length + ').';
    } else if (str.length < minLen) {
        return displayLabel + ' must be at least ' + minLen + ' characters long (you entered ' + str.length + ').';
    }
    return true;
}

function isValidEmail(email, displayLabel) {
    var result;
    if (!displayLabel) {
        displayLabel = 'Email';
    }
    result = isValidString(email, displayLabel, 1, 255);
    if (result==true) {
        if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email)) {
            result = 'Invalid ' + displayLabel + ': ' + email + '.';
        }
    }
    return result;
}

function validateEmail(inputId, displayLabel) {
    var input = jQuery('#' + inputId);
    var errorMsg = isValidEmail(input.val(), displayLabel);
    if (errorMsg != true) {
        return messageBoxError(errorMsg, inputId);
    }
    return true;
}

function validateString(inputId, displayLabel, minLen, maxLen) {
    var input = jQuery('#'+inputId);
    var errorMsg = isValidString(input.val(), displayLabel, minLen, maxLen);
    if (errorMsg != true) {
        return messageBoxError(errorMsg, inputId);
    }
    return true;
}
