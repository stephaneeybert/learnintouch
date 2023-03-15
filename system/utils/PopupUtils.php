<?php

class PopupUtils {

  var $mlText;

  var $languageUtils;
  var $templateUtils;

  // Constructor
  function __construct() {
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Display a dialog popup window
  function getDialogPopup($anchor, $url, $width, $height, $title = '') {
    if (!$url) {
      return;
    }

    return(LibJavaScript::openDialogPopup($anchor, $url, $title, 200, 100, $width, $height, 1));
  }

  // Display a dialog popup window at page loading time
  function getAutoDialogPopup($url, $strCss, $width, $height, $top, $left, $delay) {
    $title = '';

    return(LibJavaScript::autoDialogPopup($url, $delay, $title, $top, $left, $width, $height, 1, $strCss));
  }

  // Display a popup window
  function getPopup($anchor, $content, $width, $height) {
    global $gPanelUrl;

    $content .= $this->getCloseButton();

    $content = LibString::escapeQuotes($content);

    $title = '';

    $content = "<html><head>"
      . "<link href=\'$gPanelUrl/css/popup.css\' rel=\'stylesheet\' type=\'text/css\' />"
      . "</head><body>$content</body>";

    return(LibJavaScript::openPopup($anchor, $content, $title, 200, 100, $width, $height, true));
  }

  // Display a popup window at page loading time
  function getAutoPopup($content, $width, $height, $delay) {
    $content = LibString::escapeQuotes($content);

    $content = "<html><head>"
      . "</head><body>$content</body>";

    $title = '';

    return(LibJavaScript::autoOpenPopup($content, $delay, $title, 200, 100, $width, $height, 1));
  }

  // Get a close button
  function getCloseButton() {
    global $gCommonImagesUrl;
    global $gImageTinyCancel;

    $this->loadLanguageTexts();

    $title = $this->mlText[0];
    // Remove any line break
    $title = LibString::stripLineBreaks($title);
    $str = "\n<br /><br />"
      . "<div style='text-align:center;'>"
      . "<a href='javascript:window.close();'>"
      . "<img src='$gCommonImagesUrl/$gImageTinyCancel' style='border-width:0px;' title='$title' />"
      . "</a>"
      . "</div>";

    return($str);
  }

  // Display a popup window for a help text
  function getHelpPopup($content, $width, $height) {
    global $gPanelUrl;
    global $gCommonImagesUrl;
    global $gImageHelp;

    $anchor = "<img src='$gCommonImagesUrl/$gImageHelp' style='border-width:0px; vertical-align:middle;' title='' alt='' />";

    $content = "<span class=\"system_tooltip\">$content</span>";

    $str = "<span class='tooltip' title='$content'>$anchor</span>";

    return($str);
  }

  // Display a popup window for a label tip
  function getTipPopup($anchor, $content, $width, $height) {
    global $gPanelUrl;
    global $gCommonImagesUrl;
    global $gImageQuestion;

    // If the anchor does not yet contain an image then add a question mark image
    if (!strstr(strtolower($anchor), "<img")) {
      $anchor .= " <img src='$gCommonImagesUrl/$gImageQuestion' style='border-width:0px; vertical-align:middle; margin-bottom:2px;' alt='' />";
    }

    $content = "<span class=\"system_tooltip\">$content</span>";

    $str = "<span class='tooltip' title='$content' style='margin-right:4px;'>$anchor</span>";

    return($str);
  }

  // Display a popup window for a label tip
  function getUserTipPopup($anchor, $content, $width, $height) {
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    // If the anchor is not an image then display a question mark image
    if (!strstr(strtolower($anchor), "<img")) {
      $anchor = "<span style='white-space:nowrap;'>" . $anchor . " <img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK . "' style='border-width:0px; vertical-align:middle;' alt='' /></span>";
    }

    $content = "<span class=\"system_tooltip\">$content</span>";

    if (!$gIsPhoneClient) {
      $str = "<span class='tooltip' title='$content'>$anchor</span>";
    } else {
      $str = "<span onclick=\"var contentElement = this.getElementsByTagName('span')[1]; toggleElementInline(contentElement); return false;\" style='cursor:pointer;' title=''>"
        . $anchor
        . "<span style='display:none;'>$content</span>"
        . "</span>";
    }

    return($str);
  }

  // Display a user popup window
  function getUserPopup($anchor, $content, $width, $height) {
    global $gTemplateUrl;
    global $gImagesUserUrl;

    $content = $this->templateUtils->renderPopup($content);

    $content = LibString::escapeQuotes($content);

    return(LibJavaScript::openPopup($anchor, $content, '', 200, 100, $width, $height, true));
  }

}

?>
