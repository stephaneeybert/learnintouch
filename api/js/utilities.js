// Check if a variable is not undefined
function isNotUndefined(variableName) {
    return (typeof(window[variableName]) == "undefined") ? false : true;
}

function sleepFor(sleepDuration) {
  var now = new Date().getTime();
  while(new Date().getTime() < now + sleepDuration){ /* do nothing */ } 
}

// Add an event handler
function addEventHandler(obj, evt, callbackFunction) {
    window.attachEvent ? obj.attachEvent(evt, callbackFunction) : obj.addEventListener(evt.replace(/^on/i, ""), callbackFunction, false);
}

// Execute a function if no key were pressed for a delay in milliseconds
function throttle(fn, delay) {
    var timer = null;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fn.apply(context, args);
        }, delay);
    };
}
// Usage:
// $('input.username').keypress(throttle(function (event) {
// do something if no keypress for 250ms
// }, 250));
// See http://benalman.com/projects/jquery-throttle-debounce-plugin/
// http://javascriptweblog.wordpress.com/2010/07/19/a-javascript-function-guard/

function getUserSelectedText(pageDocument, element) {
    var selectedText;
    if (pageDocument.selection != undefined) {
        element.focus();
        if (selection.getSelection) {
          selectedText = selection.getSelection().text;
        } else if (selection.getSelectedText) {
          selectedText = selection.getSelectedText();
        } else if (selection.getNative) {
          selectedText = selection.getNative().createRange().text;
        }
    } else if (element.selectionStart != undefined) {
        var startPos = element.selectionStart;
        var endPos = element.selectionEnd;
        selectedText = element.value.substring(startPos, endPos)
    }
    return(selectedText);
}

// Count the number of words, that is, excluding punctuation
function NOT_USED_countNbRealWords(str) {
    var nb = 0;

    str = str.replace(' +', ' ');
    //  str = str.replace(/(^\s*)|(\s*$)/g, ' ');

    var bits = str.split(' ');

    for (var i = 0; i < bits.length; i++) {
        if (eregi("[0-9A-Za-zÀ-ÖØ-öø-ÿ]", bits[i])) {
            nb++;
        }
    }

    return(nb);
}

function toggleElementDisplay(buttonId, textId, textButtonShow, textButtonHide) {
    var button = document.getElementById(buttonId);
    var text = document.getElementById(textId);
    if (text.style.display == 'none') {
        text.style.display = 'block';
        button.innerHTML = textButtonHide;
    } else {
        text.style.display = 'none';
        button.innerHTML = textButtonShow;
    }
}

function toggleElementInline(text) {
    if (text.style.display == 'none') {
        text.style.display = 'inline';
    } else {
        text.style.display = 'none';
    }
}

// Do a click on the adjacent radio or checkbox input element
// so as to be able to have the label clickable
// offering an easier navigation on smartphones
function clickAdjacentInputElement(label) {
    var inputElement = label.parentNode.getElementsByTagName('input')[0];
    inputElement.checked = !(inputElement.checked);
}

function getElementsByClass(nameOfClass) {
    var foundElements = [];
    var allHTMLTags = document.getElementsByTagName("*");
    var j = 0;
    for (i = 0; i < allHTMLTags.length; i++) {
        if (allHTMLTags[i].className == nameOfClass) {
            foundElements[j] = allHTMLTags[i];
            j++;
        }
    }
    return(foundElements);
}

function stripTags(str) {
    var regExp = /(<([^>]+)>)/gi;

    str = str.replace(regExp, "");

    return(str);
}

// Toggle a table body of rows visible and invisible
// An example usage:
// $strDisplayState = "<a href='#' onClick=\"toggleTableBodyOfRows('question_$elearningQuestionId', 'button_question_$elearningQuestionId', '$gCommonImagesUrl/$gImageFolded', '$gCommonImagesUrl/$gImageCollapsed');\" style='font-weight:normal; text-decoration:none;'><img border='0' id='button_question_$elearningQuestionId' src='$gCommonImagesUrl/$gImageFolded'/>" . $question . '</a>';
function toggleTableBodyOfRows(tbodyDiv, imageDiv, imageUrlShow, imageUrlHide) {
    var tbody = document.getElementById(tbodyDiv);
    var image = document.getElementById(imageDiv);
    if (tbody.style.display == "") {
        tbody.style.display = "none";
        image.src = imageUrlShow;
    } else {
        tbody.style.display = "";
        image.src = imageUrlHide;
    }
}

// Check if a variable is set with a value
function isSet(variable) {
    return(typeof(variable) != 'undefined');
}

// Place the focus on the first form field of the page
var skipFormFocus = false;
function formFocus() {
    if (isSet(skipFormFocus) && skipFormFocus) {
        return;
    }
    if (document.forms != null && document.forms.length > 0) {
        var field = document.forms[0];
        if (field != null) {
            for (var i = 0; i < field.length; i++) {
                if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea")) {
                    if (field.elements[i] && field.elements[i].focus) {
                        try {
                            setTimeout(field.elements[i].focus(), 100);
                        } catch (error) {
                        }
                    }
                    return(field.elements[i]);
                    break;
                }
            }
        }
    }
}

// Timer count down
function updateCountdownTimer(minutes, minutesId, seconds, secondsId, timeOutFn) {
    if (seconds < 10) {
        seconds = '0' + seconds;
    }

    document.getElementById(minutesId).innerHTML = minutes;
    document.getElementById(secondsId).innerHTML = seconds;

    seconds = seconds - 1;

    if (seconds < 0) {
        seconds = 59;
        minutes = minutes - 1;
    }

    if (minutes < 0) {
        timeOutFn();
        return;
    } else {
        setTimeout("updateCountdownTimer('"+minutes+"', '"+minutesId+"', '"+seconds+"', '"+secondsId+"', "+timeOutFn+")", 1000);
    }
}

// Add an onload function
function addLoadListener(func) { 
    if (window.addEventListener) { 
        window.addEventListener("load", func, false); 
    } else if (document.addEventListener) { 
        document.addEventListener("load", func, false); 
    } else if (window.attachEvent) { 
        window.attachEvent("onload", func); 
    } else if (typeof window.onload != "function") { 
        window.onload = func; 
    } else { 
        var oldonload = window.onload; 
        window.onload = function() { 
            oldonload(); 
            func(); 
        }; 
    } 
}

// Send some content to the printer
// var printer = new Printer("<h1>Example</h1>"); printer.print();
function Printer($c) {
  var h = $c;
  return {
    print: function(){
      var d = $("<div>").html(h).appendTo("html");
      $("body").hide();
      window.print();
      d.remove();
      $("body").show();
    },
    setContent: function($c) {
      h = $c;
    }
  };
}

// Print a browser page
function printPage() {
    if (window.print) {
        setTimeout('window.print();', 200);
    }
}

// Close a browser window
function closeWindow(delay) {
    if (window) {
        setTimeout('window.close();', delay);
    }
}

// Returns a random number between 1 and number
function getRandom(number) {
    var today = new Date();
    var seed = today.getTime();
    seed = (seed*9301+49297) % 233280;
    seed = seed/(233280.0);
    return(Math.ceil(seed * number));
};

// Clear the window status
function clearStatus() {
    window.status=''; return true;
}

// Trim the blank spaces
function trim(str) {
    if (str) {
        trimmed = str.replace(/(^\s*)|(\s*$)/g,''); 
    } else {
        trimmed = '';
    }

    return(trimmed);
}

// Prevent the IE browser from drawing a border around the Flash objects
function hideFlashBorders(document) {
    flashObjects = document.getElementsByTagName("object");
    for (var i = 0; i < flashObjects.length; i++) {
        flashObjects[i].outerHTML = flashObjects[i].outerHTML;
    }
}

var getProperties = function(obj){
    var str = '';
    for (var key in obj) {
        str += "\\n" + key + ' : ' + obj[key];
    }
    return(str);
}

function basename(path, suffix) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ash Searle (http://hexmen.com/blog/)
    // +   improved by: Lincoln Ramsay
    // +   improved by: djmix
    // *     example 1: basename('/www/site/home.htm', '.htm');
    // *     returns 1: 'home'
    // *     example 2: basename('ecra.php?p=1');
    // *     returns 2: 'ecra.php?p=1'

    var b = path.replace(/^.*[\/\\]/g, '');

    if (typeof(suffix) == 'string' && b.substr(b.length-suffix.length) == suffix) {
        b = b.substr(0, b.length-suffix.length);
    }

    return b;
}

function getRelativeUrl(url) {
  var splitUrl = url.split('/');
  var host = splitUrl[0] + "//" + splitUrl[2];
  return url.replace(host, '');
}

function isUrl(url) {
    var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    if (RegExp.test(url)) {
        return true;
    } else {
        return false;
    }
}

function getUrlParameter(url, name) {
    var urlparts = url.split('?');
    if (urlparts.length > 1) {
        var parameters = urlparts[1].split('&');
        for (var i = 0; i < parameters.length; i++) {
            var paramparts = parameters[i].split('=');
            if (paramparts.length > 1 && unescape(paramparts[0]) == name) {
                return unescape(paramparts[1]);
            }
        }
    }
    return null;
}

function getYouTubeVideoId(url) {
    return getUrlParameter(url, 'v');
}

function renderYouTubeVideoUrl(youtubeVideoId) {
    var url = 'http://www.youtube.com/embed/' + youtubeVideoId;
    return(url);
}

function renderVimeoVideoUrl(vimeoVideoId) {
    var url = 'http://player.vimeo.com/video/' + vimeoVideoId;
    return(url);
}

function testUrlForMedia(pastedData) {
    var success = false;
    var media = {};
    if (pastedData.match('http(s)?://(www.|m.)?youtube|youtu\.be')) {
        if (pastedData.match('embed')) { 
            youtube_id = pastedData.split(/embed\//)[1].split('"')[0]; 
        } else { 
            youtube_id = pastedData.split(/v\/|v=|youtu\.be\//)[1].split(/[?&]/)[0]; 
        }
    media.type = "youtube";
    media.id = youtube_id;
    success = true;
} else if (pastedData.match('http(s)?://(player.)?vimeo\.com')) {
vimeo_id = pastedData.split(/video\/|http:\/\/vimeo\.com\//)[1].split(/[?&]/)[0];
media.type = "vimeo";
media.id = vimeo_id;
success = true;
} else if (pastedData.match('http(s)?://player\.soundcloud\.com')) {
soundcloud_url = unescape(pastedData.split(/value="/)[1].split(/["]/)[0]);
soundcloud_id = soundcloud_url.split(/tracks\//)[1].split(/[&"]/)[0];
media.type = "soundcloud";
media.id = soundcloud_id;
success = true;
}
if (success) { 
    return media; 
}
return false;
}

