<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$closePopupWindow = LibEnv::getEnvHttpPOST("closePopupWindow");
if ($closePopupWindow) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$mlText = $languageUtils->getMlText(__FILE__);

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

$templateElementId = LibEnv::getEnvHttpGET("templateElementId");

if ($templateElementId) {
  if ($templateElement = $templateElementUtils->selectById($templateElementId)) {
    $elementType = $templateElement->getElementType();
    $objectId = $templateElement->getObjectId();

    // Get the element content
    $content = $templateElementUtils->renderContent($templateElementId, $elementType, $objectId);

    // Get the tag ids
    $tagIDs = $templateElementUtils->getTagIDs($templateElementId, $content);

    // Update the non cached content
    $content = $templateUtils->updateContent($content);

    // Prepare the content for the style editor
    $content = $templateElementUtils->prepareContent($content);

    // Create the tags for the element (if the code source has been modified)
    $templateElementUtils->createTags($templateElementId, $tagIDs);

    // Get the template model id
    $templateModelId = '';
    if ($templateContainer = $templateContainerUtils->selectById($templateElement->getTemplateContainerId())) {
      $templateModelId = $templateContainer->getTemplateModelId();
    }

    $strTagProperties = array();
    if (count($tagIDs) > 0) {
      foreach ($tagIDs as $tagID) {
        // Get the tag
        if (!is_string($tagID)) {
          continue;
        }

        // There should ideally be only one row for a tagID but it may happen
        // after some styling that more than one row exists for a tagID
        if ($templateTags = $templateTagUtils->selectByTemplateElementIdAndTagID($templateElementId, $tagID)) {
          foreach ($templateTags as $templateTag) {
            $templateTagId = $templateTag->getId();

            $tagName = $templateTagUtils->getTagName($tagID);
            if ($tagName) {
              // Display a button to delete the styles of an element
              $strTagProperty = "<a href='$gTemplateUrl/design/element/deleteTagProperty.php?tagID=$tagID&templateElementId=$templateElementId' onclick='javascript:return confirmDelete(this);' title='' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

              // Display a remote (outside of the div tags) link to edit the properties of the tag
              $noStatus = LibJavaScript::getNoStatus();
              $url = "$gTemplateUrl/design/element/property.php?templateTagId=$templateTagId";
              $strTagProperty .= " <a href='$url' onMouseOver=\"javascript:onRemoteUnderline(document.getElementById('$tagID'));\" onMouseOut=\"javascript:onReset(document.getElementById('$tagID'));\" onclick=\"window.open(this.href, '_blank'); return(false);\" title='$tagName'><img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[4]'> $tagName</a>";

              // Render a local (inside the div tags) link to edit the properties of the tag
              $content = $templateElementUtils->insertJsEditor($content, $tagID, $url);

              array_push($strTagProperties, $strTagProperty);
            }
          }
        }
      }
    }

    $panelUtils->setHeader($mlText[0]);
    $panelUtils->addLine();

    $strUnderline = $templateElementUtils->insertJsEditorCallback();
    $panelUtils->addContent($strUnderline);

    $panelUtils->addContent($gConfirmDelete);

    $strProperties = '';
    foreach ($strTagProperties as $strTagProperty) {
      $strProperties .= '<br/>' . $strTagProperty;
    }

    $str = "<table width='100%' border='0' cellpadding='2' cellspacing='2'><tr>"
      . "<td style='vertical-align:top; white-space:nowrap;'>$strProperties</td><td valign='top'>$content</td>"
      . "</tr></table>";
    $panelUtils->addLine($str);
    $panelUtils->addLine();

    $panelUtils->openForm($PHP_SELF, 'edit');
    $closeLabel = "<a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none;'>$mlText[3]</a>";
    $panelUtils->addLine($panelUtils->addCell($panelUtils->getOk() . ' ' . $closeLabel, 'nbc'));
    $panelUtils->addHiddenField('closePopupWindow', 1);
    $panelUtils->closeForm();

    $str = $panelUtils->render();

    $strHtmlProperties = $templateElementUtils->renderHtmlProperties($templateElementId);
    if ($strHtmlProperties) {
      $strHtmlProperties = "<style type='text/css'>" . $strHtmlProperties . "\n</style>";
    }

    printAdminPage($str, $strHtmlProperties);
  }
}

?>
