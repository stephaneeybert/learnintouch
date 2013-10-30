<?php

include($gInnovaHtmlEditorPath . "setup.php");

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
<script language="javascript" type="text/javascript">

var $oInnovaName = new InnovaEditor("$oInnovaName");

$oInnovaName.useTab = false;

$oInnovaName.features=[
  "Save","FullScreen","Preview","Print", "Search","|",
  "Undo","Redo","ClearAll","XHTMLSource",
  "Cut","Copy","Paste","PasteWord","PasteText","|",
  "Image","Hyperlink","InternalLink",$customTagButton,"CustomObject",$flashButton,"Bookmark","Characters","|",
  "Table","Guidelines","|",
  "Numbering","Bullets","|","Indent","Outdent","|",
  "Line","RemoveFormat","BRK",
  "StyleAndFormatting","TextFormatting","ListFormatting",
  "BoxFormatting","ParagraphFormatting","CssText","|",
  "Paragraph","FontName","FontSize","|",
  "JustifyLeft","JustifyCenter","JustifyRight","JustifyFull","|",
  "Bold","Italic","Underline","Strikethrough","Superscript","Subscript","|",
  "ForeColor","BackColor"
  ];

$oInnovaName.btnSave=true;
$oInnovaName.btnPrint=true;
$oInnovaName.btnCut=true;
$oInnovaName.btnCopy=true;
$oInnovaName.btnPaste=true;
$oInnovaName.btnPasteText=true;
$oInnovaName.btnFlash=true;
$oInnovaName.btnStrikethrough=true;
$oInnovaName.btnSuperscript=true;
$oInnovaName.btnClearAll=true;
$oInnovaName.btnSubscript=true;
$oInnovaName.btnLTR=true;
$oInnovaName.btnRTL=true;
$oInnovaName.btnBookmark=false;
$oInnovaName.btnAbsolute=false;
$oInnovaName.btnForm=false;
$oInnovaName.width="100%";
$oInnovaName.height="600px";
$oInnovaName.mode="XHTMLBody";
$oInnovaName.btnXHTMLSource=true;
$oInnovaName.useDIV=false;
$oInnovaName.useBR=true;
$oInnovaName.PreserveSpace=true;
$oInnovaName.initialRefresh=true;
$oInnovaName.btnInternalLink=true;
$oInnovaName.cmdInternalLink = "window.open('$gInnovaHtmlEditorUrl/internalLink.php', '', 'top=200,left=100,width=700,height=300,scrollbars,resizable');";
$oInnovaName.btnCustomObject=true;
$oInnovaName.cmdCustomObject = "window.open('$gInnovaHtmlEditorUrl/lexicon.php?oInnovaContentName=$oInnovaContentName', '', 'top=200,left=100,width=700,height=500,scrollbars,resizable');";
HEREDOC;
// This call kills the admin session (unlike the one above) but does not bug down on insertLink
//$oInnovaName.cmdInternalLink = "modalDialogShow('$gInnovaHtmlEditorUrl/internalLink.php', 700, 300)";

?>
