<?php

class PanelUtils extends PanelContentUtils {

  var $mlText;

  var $header;
  var $parentUrl;
  var $help;
  var $isMainMenu;
  var $lines;
  var $linesHiddenContent;
  var $linesList;
  var $isList;
  var $cacheStatus;

  var $languageUtils;

  function PanelUtils() {
    $this->PanelContentUtils();

    $this->header = '';
    $this->parentUrl = '';
    $this->lines = array ();
    $this->linesHiddenContent = array ();
    $this->linesList = array ();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function setHeader($header = '', $parentUrl = '') {
    $this->header = $header;
    $this->parentUrl = $parentUrl;
  }

  // Set the help content
  function setHelp($help) {
    $this->help = $help;
  }

  // Set as the main menu
  function setMainMenu() {
    $this->isMainMenu = true;
  }

  // Set the cache status message
  function setCacheStatus($cacheStatus) {
    $this->cacheStatus = $cacheStatus;
  }

  // Add a file upload form
  function openMultipartForm($url, $id = '') {
    $strId = '';
    if ($id) {
      $strId = "name='" . $id . "' id='" . $id . "'";
    }

    $this->addContent("<form action='" . $url . "' method='post' enctype='multipart/form-data' $strId>");
  }

  // Open a list with items marked by a separating line
  function openList($sortableLinesClass = true) {
    $this->isList = $sortableLinesClass;
  }

  // Close a list
  function closeList() {
    $this->isList = false;
  }

  // Add opening form
  function openForm($url, $id = '', $onSubmit = '') {
    $strId = '';
    if ($id) {
      $strId = "name='" . $id . "' id='" . $id . "'";
    }

    $strOnSubmit = '';
    if ($onSubmit) {
      $strOnSubmit = "onsubmit='" . $onSubmit . "'";
    }

    $this->addContent("<form action='" . $url . "' method='post' $strId $strOnSubmit target='_self'>");
  }

  // Close a form
  function closeForm() {
    $this->addContent("</form>");
  }

  // Add a hidden form field
  function addHiddenField($name, $value) {
    $this->addContent("<input type='hidden' name='$name' id='$name' value='$value'>");
  }

  // Add a line in the layout
  function addLine() {
    $arguments = func_get_args();
    if (!$arguments) {
      $arguments = '';
    }
    $this->lines[count($this->lines)] = $arguments;
    $this->linesHiddenContent[count($this->linesHiddenContent)] = '';
    $this->linesList[count($this->linesList)] = $this->isList;
  }

  // Add some hidden content in the table
  // This content is not part of a table cell
  function addContent() {
    $this->lines[count($this->lines)] = func_get_args();
    $this->linesHiddenContent[count($this->linesHiddenContent)] = true;
    $this->linesList[count($this->linesList)] = '';
  }

  // Add a cell in a line
  function addCell($content, $styleFlags) {
    return(array($content, $styleFlags));
  }

  // Print the object properties
  function dumpProperties() {
    $str = '';
    $str .= "<br>header: " . $this->header;
    $str .= "<br>parentUrl: " . $this->parentUrl;
    for ($i = 0; $i < count($this->lines); $i++) {
      $line = $this->lines[$i];
      $lineHiddenContent = $this->linesHiddenContent[$i];
      $str .= "<br>property: " . $lineHiddenContent;
      $lineList = $this->linesList[$i];
      $str .= "<br>property: " . $lineList;
      foreach ($line as $cell) {
        if (is_array($cell)) {
          list ($content, $style) = $cell;
          $str .= "[$content::$style]";
        } else {
          $str .= "[$cell]";
        }
      }
    }
    return($str);
  }

  // Render
  function render() {
    global $gCommonImagesUrl;
    global $gCommonMenuImagesUrl;
    global $gImageAdmin;
    global $gIconLogout;
    global $gJSNoStatus;
    global $gAdminUrl;
    global $gHomeUrl;
    global $gIconLearnInTouch;
    global $gIconLearnInTouchHover;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<table border='0' id='bodyTable' class='admin' style='width:100%; height:94%;'><tbody><tr><td class='no_style_body'>";

    if ($this->header) {
      $str .= "\n<table border='0' width='100%' cellpadding='2' cellspacing='2'><tr>";
      $str .= "\n<td align='left' width='25%'>";
      if ($this->isMainMenu) {
        $title = $this->mlText[5];
        $str .= "\n<a href='http://www.thalasoft.com' target='_blank' $gJSNoStatus>";
        $str .= "\n<img src='$gCommonMenuImagesUrl/$gIconLearnInTouch' class='tipPopup' border='0' title='$title'>";
        $str .= "\n</a>";
      } else {
        if (trim($this->parentUrl)) {
          $title = $this->mlText[3];
          $str .= "\n<a href='$this->parentUrl' $gJSNoStatus>";
          $str .= "\n<img src='$gCommonImagesUrl/$gImageAdmin' border='0' title='$title'></a>";
        }
      }
      $str .= $this->cacheStatus;
      $str .= "\n</td>";
      $str .= "\n<td align='center'>";
      $str .= "\n<div class='header'>$this->header</div>";
      $str .= "\n</td>";
      $str .= "\n<td align='right' width='25%'>";
      if ($this->isMainMenu) {
        $str .= "<a href='$gAdminUrl/logout.php' id='logoutLink' $gJSNoStatus title=''>"
          . "<img src='$gCommonMenuImagesUrl/$gIconLogout' border='0' title='" . $this->mlText[2] . "'></a>";
      } else {
        $str .= $this->help;
      }
      $str .= "\n</td>";
      $str .= "\n</tr></table>";
    }

    $str .= "\n<br/><table class='list_lines' border='0' width='100%' cellpadding='4' cellspacing='0'><tbody>";
    $str .= $this->renderLines();
    $str .= "\n</tbody></table>";

    $str .= "\n</td></tr></tbody></table>";

    $strTranslate = $this->languageUtils->renderTranslateLanguageResource();

    $title = $this->mlText[6];
    $str .= "<div style='width:100%; text-align:center;'>$strTranslate <a href='http://8fub189.copyrightfrance.com' $gJSNoStatus target='_blank' title='$title'>Copyright</a> 2010 <a href='http://www.thalasoft.com' $gJSNoStatus target='_blank' title='$title'>Thalasoft</a> All Rights Reserved.</div>";

    return($str);
  }

  function renderLines() {
    $str = '';

    $this->maxCell = 0;
    foreach ($this->lines as $line) {
      $this->maxCell = max($this->maxCell, count($line));
    }

    for ($i = 0; $i < count($this->lines); $i++) {
      $str .= $this->renderLine($i);
    }

    return($str);
  }

  function renderLine($i) {
    $line = $this->lines[$i];
    $lineHiddenContent = $this->linesHiddenContent[$i];
    $lineList = $this->linesList[$i];
    $previousIndex = max(0, $i - 1);
    $previousLineList = $this->linesList[$previousIndex];
    if (!$previousLineList && $lineList) {
      $str = "\n</tbody><tbody class='" . $lineList . "'>";
    } else if ($previousLineList && !$lineList) {
      $str = "\n</tbody><tbody>";
    } else {
      $str = '';
    }
    if ($lineHiddenContent) {
      $str .= "\n$line[0]";
      return($str);
    }
    $strRowClass = '';
    if ($lineList) {
      $strRowClass .= " listline";
    }
    $str .= "\n<tr class='$strRowClass'>";
    for ($j = 0; $j < count($line); $j++) {
      $cell = LibUtils :: getArrayValue($j, $line);
      if (!$lineHiddenContent) {
        if (!$cell) {
          $cell = '&nbsp;';
        } else if (is_array($cell) && count($cell) > 0 && !$cell[0]) {
          $cell = '&nbsp;';
        }
      }
      $strStyle = '';
      $strAttribute = '';
      if (is_array($cell)) {
        if (count($cell) == 2) {
          list ($content, $style) = $cell;
        } else {
          $content = $cell;
          $style = '';
        }
        if ($style) {
          // Add the style for the visible lines only
          if (!$lineHiddenContent) {
            if ($width = $this->getWidth($style)) {
              $strStyle .= " width:$width;";
            }
            if ($this->isWarning($style)) {
              $strStyle .= " font-weight:bold; font-size:normal; color:red;";
            }
            if ($this->isGreen($style)) {
              $strStyle .= " font-weight:normal; font-size:normal; color:green;";
            }
            if ($this->isBold($style)) {
              $strStyle .= " font-weight:bold;";
            }
            if ($this->isLeft($style)) {
              $strAttribute .= " align='left'";
            }
            else if ($this->isRight($style)) {
              $strAttribute .= " align='right'";
            }
            else if ($this->isCenter($style)) {
              $strAttribute .= " align='center'";
            }
            if ($this->isNoWrap($style)) {
              $strAttribute .= " nowrap";
            }
            if ($this->isMiddle($style)) {
              $strAttribute .= " valign='middle'";
            } else {
              $strAttribute .= " valign='top'";
            }
          }
        }
      } else {
        $content = "$cell";
      }

      // If a line has less cells than the others then apply
      // a colspan on its first cell
      $colspan = $this->maxCell -count($line) + 1;
      if ($colspan > 1 && $j == 0) {
        $strAttribute .= " colspan=$colspan";
      }

      $str .= "\n<td style='$strStyle' width='10%' $strAttribute>";
      $str .= "\n$content";
      $str .= "\n</td>";
    }

    $str .= "\n</tr>";

    return($str);
  }

  function isWarning($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'w') {
        return(true);
      }
    }

    return(false);
  }

  function isGreen($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'g') {
        return(true);
      }
    }

    return(false);
  }

  function getWidth($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'z') {
        $width = substr($style, $i + 1, 2) . '%';
        return($width);
      }
    }
  }

  function isBold($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'b') {
        return(true);
      }
    }

    return(false);
  }

  function isLeft($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'l') {
        return(true);
      }
    }

    return(false);
  }

  function isRight($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'r') {
        return(true);
      }
    }

    return(false);
  }

  function isCenter($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'c') {
        return(true);
      }
    }

    return(false);
  }

  function isTop($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 't') {
        return(true);
      }
    }

    return(false);
  }

  function isMiddle($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'm') {
        return(true);
      }
    }

    return(false);
  }

  function isBottom($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'B') {
        return(true);
      }
    }

    return(false);
  }

  function isNoWrap($style) {
    for ($i = 0; $i < strlen($style); $i++) {
      $char = substr($style, $i, 1);
      if ($char == 'n') {
        return(true);
      }
    }

    return(false);
  }

}
?>
