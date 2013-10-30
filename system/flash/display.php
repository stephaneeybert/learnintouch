<?php

require_once("website.php");

$preferenceUtils->init($dynpageUtils->preferences);
$displayConstructionMessage = $preferenceUtils->getValue("DYNPAGE_WEBSITE_IN_CONSTRUCTION");

if ($displayConstructionMessage) {
  $preferenceUtils->init($dynpageUtils->preferences);
  $str = nl2br($preferenceUtils->getValue("DYNPAGE_WEBSITE_IN_CONSTRUCTION_MESSAGE"));

  $gTemplate->setPageContent($str);
  require_once($gTemplatePath . "render.php");
} else {
  // Check if the intro must be skipped
  $skipIntro = LibEnv::getEnvHttpGET("skipIntro");

  // Check if the intro should be displayed only once per visit
  $preferenceUtils->init($flashUtils->preferences);
  $displayOnce = $preferenceUtils->getValue("FLASH_INTRO_DISPLAY_ONCE");

  if ($displayOnce) {
    // Check if the intro has already been displayed
    $wasDisplayed = LibSession::getSessionValue(FLASH_SESSION_WAS_DISPLAYED);

    // Remember the intro has been displayed
    LibSession::putSessionValue(FLASH_SESSION_WAS_DISPLAYED, true);
  }

  // Check if the intro has a display period
  $displayPeriod = $preferenceUtils->getValue("FLASH_INTRO_DISPLAY_PERIOD");

  if (is_numeric($displayPeriod) && $displayPeriod > 0) {
    // Get the last visit date
    $wasDisplayedInPeriod = LibCookie::getCookie($flashUtils->wasDisplayedInPeriod);

    $systemDate = $clockUtils->getSystemDate();
    $cookieDuration = (60 * 60 * 24 * $displayPeriod);
    LibCookie::putCookie($flashUtils->wasDisplayedInPeriod, true, $cookieDuration);
  } else {
    $wasDisplayedInPeriod = false;
  }

  $hidden = $preferenceUtils->getValue("FLASH_INTRO_HIDDEN");

  // Get the name of the Flash intro file
  $flashIntroFile = $flashUtils->getIntroFlashName();

  // Check if the flash intro should be displayed
  if (!($wasDisplayedInPeriod) && !($displayOnce && $wasDisplayed) && !$skipIntro && !$hidden && $flashIntroFile) {
    $inPopup = $preferenceUtils->getValue("FLASH_INTRO_POPUP");
    if (!$inPopup) {

      $title = $profileUtils->getWebsiteTitle();
      $favicon = $profileUtils->renderFavicon();
      $iPhoneicon = $profileUtils->renderIPhoneIcon();

      $preferenceUtils->init($flashUtils->preferences);
      $bgcolor = $preferenceUtils->getValue("FLASH_INTRO_PAGE_BG_COLOR");

      $flashIntro = $flashUtils->renderFlashIntroObject();

      $skipLink = $preferenceUtils->getValue("FLASH_INTRO_SKIP_LINK");
      if (!$skipLink) {
        $skipLink = $flashUtils->websiteText[20];
      }

      // Note that the first line of the page must be the doctype one
      // Otherwise IE 6 turns into quirks mode with its well known "IE box model bug"
      // So no such line as xml version="1.0" encoding="ISO-8859-1"
      // shall be the first line
      //<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      //"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      //<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      $str = <<<HEREDOC
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>$title</title>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv='content-script-type' content='text/javascript' />
<meta http-equiv='content-style-type' content='text/css' />
<meta http-equiv='imagetoolbar' content='false' />
<meta http-equiv='content-language' content='fr' />
$favicon
$iPhoneicon
</head>
<body style='background-color:$bgcolor;'>
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr><td align='center'>
$flashIntro
</td></tr>
<tr><td align='center'>
<br />
<form action='index.php' method='get'>
<div><input type="submit" value="$skipLink" /></div>
<div><input type='hidden' name='skipIntro' value='1' /></div>
</form>
<!-- <a href='engine/system/template/display.php'>$skipLink</a> -->
</td></tr>
</table>
</body>
</html>
HEREDOC;

      print($str);
    } else {
      // Get the width and height of the popup window
      $width = $preferenceUtils->getValue("FLASH_INTRO_POPUP_WIDTH");
      $height = $preferenceUtils->getValue("FLASH_INTRO_POPUP_HEIGHT");
      $top = $preferenceUtils->getValue("FLASH_INTRO_POPUP_TOP");
      $left = $preferenceUtils->getValue("FLASH_INTRO_POPUP_LEFT");

      // Render the popup
      $strCss = $templateUtils->getModelCssUrl();
      $str = $popupUtils->getAutoDialogPopup("$gFlashUrl/popup.php", $strCss, $width, $height, $top, $left, 0);
      print($str);

      require_once($gTemplatePath . "display.php");
    }
  } else {
    require_once($gTemplatePath . "display.php");
  }

}

?>
