<?php

class PanelContentUtils {

  var $mlText;

  var $languageUtils;

  function __construct() {
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function getOk() {
    global $gCommonImagesUrl;
    global $gImageOk;

    $this->loadLanguageTexts();

    $title = $this->mlText[0];

    $str = "<input type='image' border='0' name='okButton' id='okButton' src='$gCommonImagesUrl/$gImageOk' title='$title'>";

    return($str);
  }

  function getTinyOk() {
    global $gCommonImagesUrl;
    global $gImageTinyOk;

    $this->loadLanguageTexts();

    $title = $this->mlText[0];

    $str = "<input type='image' border='0' style='vertical-align:middle;' name='okButton' id='okButton' src='$gCommonImagesUrl/$gImageTinyOk' title='$title'>";

    return($str);
  }

  function getCancel() {
    global $gCommonImagesUrl;
    global $gImageCancel;

    $this->loadLanguageTexts();

    $title = $this->mlText[1];

    $str = "<input type='image' border='0' name='cancelButton' id='cancelButton' src='$gCommonImagesUrl/$gImageCancel' title='$title'>";

    return($str);
  }

  function getTinyCancel() {
    global $gCommonImagesUrl;
    global $gImageTinyCancel;

    $this->loadLanguageTexts();

    $title = $this->mlText[1];

    $str = "<input type='image' border='0' name='cancelButton' id='cancelButton' src='$gCommonImagesUrl/$gImageTinyCancel' title='$title'>";

    return($str);
  }

}

?>
