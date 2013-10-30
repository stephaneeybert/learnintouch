<?php

class PanelContentUtils {

  function PanelContentUtils() {
  }

  function getOk() {
    global $gCommonImagesUrl;
    global $gImageOk;

    $title = $this->mlText[0];

    $str = "<input type='image' border='0' name='okButton' id='okButton' src='$gCommonImagesUrl/$gImageOk' title='$title'>";

    return($str);
  }

  function getTinyOk() {
    global $gCommonImagesUrl;
    global $gImageTinyOk;

    $title = $this->mlText[0];

    $str = "<input type='image' border='0' style='vertical-align:middle;' name='okButton' id='okButton' src='$gCommonImagesUrl/$gImageTinyOk' title='$title'>";

    return($str);
  }

  function getCancel() {
    global $gCommonImagesUrl;
    global $gImageCancel;

    $title = $this->mlText[1];

    $str = "<input type='image' border='0' name='cancelButton' id='cancelButton' src='$gCommonImagesUrl/$gImageCancel' title='$title'>";

    return($str);
  }

  function getTinyCancel() {
    global $gCommonImagesUrl;
    global $gImageTinyCancel;

    $title = $this->mlText[1];

    $str = "<input type='image' border='0' name='cancelButton' id='cancelButton' src='$gCommonImagesUrl/$gImageTinyCancel' title='$title'>";

    return($str);
  }

}

?>
