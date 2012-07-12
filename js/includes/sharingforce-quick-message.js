/*
 * Public functions:
 * - ShowQuickMessage(HTML, ShowImmediately)
 * - ShowQuickMessage_Error(HTML)
 */

var ANIMATE_OPACITY_DELAY = 2;

function showQuickMessage(html, isShownImmediately) {
    quickMessageCreateDomAndAddEventListeners();
    quickMessageHtml = html;
    divQuickMessage.className = 'divQuickMessage divQuickMessage_Normal';
    doShowQuickMessage(html, isShownImmediately);
}

function showQuickMessageError(html) {
    quickMessageCreateDomAndAddEventListeners();
    divQuickMessage.className = 'divQuickMessage divQuickMessage_Error';
    doShowQuickMessage(html);
}

var divQuickMessage, showHideQuickMessageCallback;

function showHideQuickMessage(show, isShownImmediately) {
    quickMessageCreateDomAndAddEventListeners();
    if (showHideQuickMessageCallback) {
        showHideQuickMessageCallback(show, isShownImmediately);
    } else {
        if (show) {
            if (isShownImmediately) {
                divQuickMessage_SetOpacity(100);
            } else {
                divQuickMessage_AnimateOpacity(0, 100);
            }
        } else {
            divQuickMessage_AnimateOpacity(100, 0);
        }
    }
}

function divQuickMessage_SetOpacity(opacity) {
    if (!opacity) {
        divQuickMessage.style.display = 'none';
    } else {
        divQuickMessage.style.display = 'block';
    }
    if (typeof(divQuickMessage.style.opacity) == 'undefined') {
        divQuickMessage.style.zoom = 1;
        divQuickMessage.style.filter = 'alpha(opacity="' + opacity + '")';
    } else {
        divQuickMessage.style.opacity = opacity/100;
    }
}

var divQuickMessage_Opacity, divQuickMessage_TargetOpacity, divQuickMessage_AnimateOpacityTimeout;

function divQuickMessage_AnimateOpacity(StartOpacity, TargetOpacity) {
  divQuickMessage_AnimateOpacityTimeout = null;
  divQuickMessage_Opacity = StartOpacity;
  divQuickMessage_TargetOpacity = TargetOpacity;
  divQuickMessage_AnimateOpacityStep();
}

function divQuickMessage_AnimateOpacityStep() {
  if(divQuickMessage_Opacity<divQuickMessage_TargetOpacity)
    divQuickMessage_Opacity += 5;
  else if(divQuickMessage_Opacity>divQuickMessage_TargetOpacity)
    divQuickMessage_Opacity -= 5;
  else
    return;
  divQuickMessage_SetOpacity(divQuickMessage_Opacity);
  divQuickMessage_AnimateOpacityTimeout = setTimeout(divQuickMessage_AnimateOpacityStep,
        ANIMATE_OPACITY_DELAY);
}

function quickMessageCreateDomAndAddEventListeners() {
  if(divQuickMessage)
    return;
  divQuickMessage = document.createElement('div');
  divQuickMessage.className = 'divQuickMessage';
  document.body.appendChild(divQuickMessage);
  setOnWindowResize(QuickMessage_OnWindowResize);
  divQuickMessage.onmouseover = QuickMessage_OnMouseEnter;
  divQuickMessage.onmouseout = QuickMessage_OnMouseLeave;
}

var QuickMessageTimeout;

function doShowQuickMessage(HTML, ShowImmediately) {
  clearTimeout(QuickMessageTimeout);
  QuickMessageTimeout = setTimeout(HideQuickMessage, 3000);
  divQuickMessage.innerHTML = HTML;
  showHideQuickMessage('show', ShowImmediately);
  QuickMessage_OnWindowResize();
}

function HideQuickMessage() {
  quickMessageHtml = null;
  showHideQuickMessage();
}

function QuickMessage_OnWindowResize() {
  var pageSize = getPageSize();
  divQuickMessage.style.left = (pageSize.width-divQuickMessage.offsetWidth)/2+'px';
}

function QuickMessage_OnMouseEnter() {
  clearTimeout(QuickMessageTimeout);
}

function QuickMessage_OnMouseLeave() {
  QuickMessageTimeout = setTimeout(HideQuickMessage, 2000);
}
