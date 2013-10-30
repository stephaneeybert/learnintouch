<?php

include($gInnovaHtmlEditorPath . "setup.php");

if (!isset($oWidth)) {
  $oWidth = '"400px"';
  }

if (!isset($oHeight)) {
  $oHeight = '"240px"';
  }

if (isset($withCustomTagButton)) {
  $customTagButton = '"CustomTag",';
  } else {
  $customTagButton = '';
  }

if (isset($withFlashButton)) {
  $flashButton = '"Flash",';
  } else {
  $flashButton = '';
  }

$oInnovaName = 'innova' . $oInnovaContentName;

$gInnovaBodyOpen = $gInnovaJS . <<<HEREDOC
<script type='text/javascript'>

var $oInnovaName = new InnovaEditor("$oInnovaName");

$oInnovaName.useTab = false;

$oInnovaName.features=[
  "Hyperlink","InternalLink","|",
  "RemoveFormat","XHTMLSource"
  ];

$oInnovaName.btnSave=false;
$oInnovaName.btnPrint=false;
$oInnovaName.btnCut=false;
$oInnovaName.btnCopy=false;
$oInnovaName.btnPaste=false;
$oInnovaName.btnPasteText=false;
$oInnovaName.btnFlash=false;
$oInnovaName.btnStrikethrough=false;
$oInnovaName.btnSuperscript=false;
$oInnovaName.btnClearAll=false;
$oInnovaName.btnSubscript=false;
$oInnovaName.btnLTR=false;
$oInnovaName.btnRTL=false;
$oInnovaName.btnBookmark=false;
$oInnovaName.btnAbsolute=false;
$oInnovaName.btnForm=false;
$oInnovaName.width=$oWidth;
$oInnovaName.height=$oHeight;
$oInnovaName.mode="XHTMLBody";
$oInnovaName.btnXHTMLSource=true;
$oInnovaName.useDIV=false;
$oInnovaName.useBR=true;
$oInnovaName.PreserveSpace=true;
$oInnovaName.initialRefresh=true;
$oInnovaName.btnInternalLink=true;
$oInnovaName.cmdInternalLink = "window.open('$gInnovaHtmlEditorUrl/internalLink.php', '', 'top=200,left=100,width=700,height=300,scrollbars,resizable');";
HEREDOC;

?>
