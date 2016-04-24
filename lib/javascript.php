<?php

class LibJavaScript {

  // Remember the scrolling position in the window
  static function rememberScroll($scrollName) {
    $str = <<<HEREDOC
<script type='text/javascript'>
window.captureEvents(Event.CLICK);
window.onclick = storeVerticalScrollPosition;

function storeVerticalScrollPosition(e) {
  var scroll = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
  setCookie("$scrollName", scroll, 1);
  return true;
}

function retrieveVerticalScrollPosition() {
  var scroll = getCookie("$scrollName");
  if (scroll) {
    window.scrollTo(0, scroll);
    }
  }
addDOMLoadEvent(retrieveVerticalScrollPosition);
</script>
HEREDOC;

    return($str);
  }

  // Render the jquery datepicker language code
  static function renderJQueryDatepickerLanguageCode($languageCode) {
    if ($languageCode == 'dk') {
      $code = 'da';
    } else if ($languageCode == 'en' || !$languageCode) {
      $code = 'en-GB';
    } else {
      $code = $languageCode;
    }

    return($code);
  }

  // Turn off the display of a dotted border on a selected image link
  static function getNoDottedBorder() {
    $str = "onFocus='if (this.blur) this.blur();'";

    return($str);
  }

  // Get javascript to empty the status bar display
  // Turn off the display in the browser status window
  static function getNoStatus() {
    $str = "onmouseover=\"(window.status=''); return(true);\"";

    return($str);
  }

  // Get javascript script to display a dialog popup window
  static function openDialogPopup($anchor, $url, $title, $left, $top, $width, $height, $scrollbar) {
    $noStatus = LibJavaScript::getNoStatus();

    $str = "<a href='#' " . $noStatus . " onclick=\"popup = dialogPopupNew('$url', '', $left, $top, $width, $height, $scrollbar); return(false);\" title='$title' style='text-decoration:none;'>" . $anchor . "</a>";

    return($str);
  }

  // Get javascript script to display a popup window
  static function openPopup($anchor, $content, $title, $left, $top, $width, $height, $scrollbar, $mouseOver = false, $keepOpen = false) {
    $noStatus = LibJavaScript::getNoStatus();

    $content = LibString::stripNonTextChar($content);
    $content = htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8');
    $content = LibString::lineBreakToSpace($content);

    if ($mouseOver) {
      $strMouseIn = "onmouseover";
    } else {
      $strMouseIn = "onclick";
    }

    $strMouseOut = '';
    if ($mouseOver) {
      if (!$keepOpen) {
        $strMouseOut = "onmouseout=\"popup.close();\"";
      }
    }

    $str = "<a href='#' $noStatus $strMouseIn=\"popup = popupNew('$content', '$title', $left, $top, $width, $height, $scrollbar); return(false);\" $strMouseOut style='text-decoration:none;'>$anchor</a>";

    return($str);
  }

  // Display a content popup window at page load time
  static function autoDialogPopup($url, $delay, $title, $top, $left, $width, $height, $scrollbar, $css) {
    $str = LibJavaScript::getJSLib();
    $str .= "<script type='text/javascript'>popup = autoDialogNew('$url', $delay, '$title', '$left', '$top', '$width', '$height', '$scrollbar');</script>";

    return($str);
  }

  // Display a content popup window at page load time
  static function autoOpenPopup($content, $delay, $title, $left, $top, $width, $height, $scrollbar) {
    $content = LibString::stripNonTextChar($content);
    $content = htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8');
    $content = LibString::lineBreakToSpace($content);

    $str = LibJavaScript::getJSLib();
    $str .= "<script type='text/javascript'><![CDATA[ popup = autoPopupNew(\"content\", $delay, '$title', $left, $top, $width, $height, $scrollbar); ]]></script>";

    return($str);
  }

  // Get the javascript library
  static function getJSLib() {
    global $gJsUrl;

    $str = <<<HEREDOC
<script src='$gJsUrl/utilities.js' type='text/javascript'></script>
<script src='$gJsUrl/popup.js' type='text/javascript'></script>
HEREDOC;

    return($str);
  }

  // Print the page content when clicking
  static function printOnClick($anchor) {
    $noStatus = LibJavaScript::getNoStatus();

    $str = LibJavaScript::getJSLib();
    $str .= "<a href='#' $noStatus onclick='printPage(); return(false);' style='text-decoration:none;'>$anchor</a>";

    return($str);
  }

  // Close automatically the document window
  static function autoCloseWindow($seconds = 0) {
    $str = LibJavaScript::getJSLib();
    $str .= "<script type='text/javascript'>closeWindow($seconds * 1000);</script>";

    return($str);
  }

  // Close the document window when clicking on a link
  static function closeWindow($anchor) {
    $noStatus = LibJavaScript::getNoStatus();

    $str = LibJavaScript::getJSLib();
    $str .= "<a href='#' $noStatus onclick='closeWindow(0);' style='text-decoration:none;'>$anchor</a>";

    return($str);
  }

  // Reload the parent window
  static function reloadParentWindow($reloadPost = false) {
    // Check if the POST form variables must be reloaded
    if ($reloadPost) {
      $str = "<script type='text/javascript'>window.opener.location.reload();</script>";
    } else {
      // The location.reload() function is not used for it asks for the form post variables
      $str = "<script type='text/javascript'>window.opener.location = window.opener.location.href;</script>";
    }

    return($str);
  }

}

?>
