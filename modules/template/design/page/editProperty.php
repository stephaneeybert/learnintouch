<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
if (!$templateModelId) {
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");
}
$systemPage = LibEnv::getEnvHttpGET("systemPage");

$gConfirmDelete = <<<HEREDOC
<script>
function confirmDelete() {
  confirmation = confirm('$mlText[5]');
  if (confirmation) {
    return(true);
  }

  return(false);
}
</script>
HEREDOC;

// Get the template page or create it if it does not yet exist
if ($templatePage = $templatePageUtils->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
  $templatePageId = $templatePage->getId();
} else {
  $templatePageId = $templatePageUtils->add($templateModelId, $systemPage);
}

// Get the page content
$content = $templatePageTagUtils->renderSystemPageContent($systemPage);

$strTagProperties = array();

// Insert an id attribute before all class attributes
// to serve as anchor for the underline javascript function
if ($content) {

  // Get the tag ids
  $tagIDs = $templatePageUtils->getTagIDs($templatePageId, $content);

  // Prepare the content for the style editor
  $content = $templateElementUtils->prepareContent($content);

  // Create the tags for the page (if the code source has been modified)
  $templatePageUtils->createTags($templatePageId, $tagIDs);

  foreach ($tagIDs as $tagID) {
    if (!is_string($tagID)) {
      continue;
    }

    // There should normally be only one row for a tagID but it may happen
    // after some styling that more than one row exists for a tagID
    if ($templatePageTags = $templatePageTagUtils->selectByTemplatePageIdAndTagID($templatePageId, $tagID)) {
      foreach ($templatePageTags as $templatePageTag) {
        $templatePageTagId = $templatePageTag->getId();
        $tagID = $templatePageTag->getTagID();

        // Display a button to delete the line in the cache file for an element
        $strTagProperty = "<a href='$gTemplateUrl/design/page/deleteTagProperty.php?systemPage=$systemPage&templateModelId=$templateModelId&templatePageId=$templatePageId&tagID=$tagID' onclick='javascript:return confirmDelete(this);' title='' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

        // Display a remote (outside of the div tags) link to edit the properties of the tag
        $editUrl = "$gTemplateUrl/design/page/property.php?templatePageTagId=$templatePageTagId";
        $strTagProperty .= " <a href='$editUrl' onMouseOver=\"javascript:onRemoteUnderline(document.getElementById('$tagID'));\" onMouseOut=\"javascript:onReset(document.getElementById('$tagID'));\" onclick=\"window.open(this.href, '_blank'); return(false);\" title='$mlText[4] $tagID'><img border='0' src='$gCommonImagesUrl/$gImageProperty' title=''> $tagID</a>";

        // Render a local (inside the div tags) link to edit the properties of the tag
        $content = $templateElementUtils->insertJsEditor($content, $tagID, $editUrl);

        array_push($strTagProperties, $strTagProperty);
      }
    }
  }

} else {

  $str = $mlText[3];
  printMessage($str);
  return;

}

$systemPages = $templatePageUtils->getPageList();
$headerTitle = $mlText[0] . ' "' . $systemPages[$systemPage] . '"';

$panelUtils->setHeader($headerTitle, "$gTemplateUrl/design/page/admin.php?templateModelId=$templateModelId");

$strUnderline = $templateElementUtils->insertJsEditorCallback();
$panelUtils->addContent($strUnderline);

$panelUtils->addContent($gConfirmDelete);

$strProperties = '';
foreach ($strTagProperties as $strTagProperty) {
  $strProperties .= '<br/>' . $strTagProperty;
}

$str = "<table width='100%' border='0' cellpadding='2' cellspacing='2'><tr>"
  . "<td style='vertical-align:top; white-space:nowrap; width:30%;'>$strProperties</td><td valign='top'>$content</td>"
  . "</tr></table>";
$panelUtils->addLine($str);

$str = $panelUtils->render();

$strHtmlProperties = $templatePageUtils->renderHtmlProperties($templatePageId);
if ($strHtmlProperties) {
  $strHtmlProperties = "<style type='text/css'>" . $strHtmlProperties . "\n</style>";
}

printAdminPage($str, $strHtmlProperties);

$templatePageTagUtils->cleanupDatabaseTagIDs($templatePageId, $tagIDs);

?>
