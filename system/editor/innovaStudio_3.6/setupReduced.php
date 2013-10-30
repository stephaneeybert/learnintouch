<?php

include($gInnovaHtmlEditorPath . "setup.php");

if (!isset($oWidth)) {
  $oWidth = '"500px"';
}

if (!isset($oHeight)) {
  $oHeight = '"240px"';
}

if ($withoutImageButton) {
  $imageButton = '';
} else {
  $imageButton = '"Image"';
}

$oInnovaName = 'innova' . $oInnovaContentName;

$gInnovaBodyOpen = $gInnovaJS . <<<HEREDOC
<script type='text/javascript'>

var $oInnovaName = new InnovaEditor("$oInnovaName");

$oInnovaName.useTab = false;

$oInnovaName.features=[
  "Save",$imageButton,"Hyperlink","InternalLink","CustomObject","|",
  "Paste","PasteWord","PasteText","|",
  "Bold","Italic","Underline","|",
  "ForeColor","BackColor","|",
  "RemoveFormat","XHTMLSource"
  ];

$oInnovaName.btnPrint=false;
$oInnovaName.btnCut=false;
$oInnovaName.btnCopy=false;
$oInnovaName.btnPaste=true;
$oInnovaName.btnPasteText=true;
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
$oInnovaName.btnCustomObject=true;
$oInnovaName.cmdCustomObject = "window.open('$gInnovaHtmlEditorUrl/lexicon.php?oInnovaContentName=$oInnovaContentName', '', 'top=200,left=100,width=700,height=600,scrollbars,resizable');";
HEREDOC;

?>
