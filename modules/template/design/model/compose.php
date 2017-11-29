<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateModelUtils->templateContainerUtils = $templateContainerUtils;
$templateContainerUtils->templateModelUtils = $templateModelUtils;

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");

$warnings = array();

require_once($gTemplateDesignPath . "model/composeController.php");

$name = '';
$description = '';
$modelType = '';
$parentId = '';
if ($templateModelId) {
  if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
    $name = $templateModel->getName();
    $description = $templateModel->getDescription();
    $modelType = $templateModel->getModelType();
    $parentId = $templateModel->getParentId();
  }
}

$hasChildren = $templateModelUtils->hasChildren($templateModelId);

$gConfirmDelete = <<<HEREDOC
<script>
function confirmDelete() {
  confirmation = confirm('$mlText[13]');
  if (confirmation) {
    return(true);
  }

  return(false);
}
</script>
HEREDOC;

$strJavascript = <<<HEREDOC
<script>
function underlineBorder(element) {
  element.style.border = 'dashed 1px #CC0000';
}

function resetBorder(element) {
  element.style.border = 'solid 1px #000000';
}
</script>
HEREDOC;

if (!$parentId && !$hasChildren) {
  $strAddRow = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
    . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageAddSmall' title='$mlText[21]'>"
    . "\n<input type='hidden' name='addRow' value='1'>"
    . "\n</form>";
} else {
  $strAddRow = '';
}

$strInnerModelProperty = "<a href='#' onMouseOver=\"javascript:underlineBorder(document.getElementById('innerModel'));\" onMouseOut=\"javascript:resetBorder(document.getElementById('innerModel'));\" onClick=\"popup = dialogPopupNew('$gTemplateUrl/design/model/innerProperty.php?templateModelId=$templateModelId', '', 200, 100, 700, 700, 1);\" title='$mlText[15]'>$mlText[15] <img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[15]'></a>";

$strTable = "\n<table width='100%' border='0' cellpadding='2' cellspacing='2' id='innerModel' style='padding:1px; border-style:solid; border-width:1px;'><tr>"
  . "\n<td valign='top'>"
  . $strAddRow
  . "</td><td align='right' valign='top'>"
  . $strInnerModelProperty
  . "</td></tr><tr><td colspan='2'>";

if ($templateContainers = $templateContainerUtils->selectByTemplateModelId($templateModelId)) {
  $elementList = $templateElementUtils->getGrantedElements();
  $strSelectElements = LibHtml::getSelectList("elementType", $elementList, '', true);

  $row = -1;
  foreach ($templateContainers as $templateContainer) {
    $previousRow = $row;
    $row = $templateContainer->getRow();
    if ($row > $previousRow) {
      if (!$parentId && !$hasChildren) {
        $strSwapRowContainersWithPrevious = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[29]'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='swapRow' value='$row'>"
          . "\n<input type='hidden' name='swapRowContainersWithPrevious' value='1'>"
          . "\n</form>";

        $strSwapRowContainersWithNext = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='swapRow' value='$row'>"
          . "\n<input type='hidden' name='swapRowContainersWithNext' value='1'>"
          . "\n</form>";
      } else {
        $strSwapRowContainersWithPrevious = '';
        $strSwapRowContainersWithNext = '';
      }

      if ($parentId) {
        if (!$templateModelUtils->sameChildParentContainerRowsWidth($templateModelId, $parentId, $row)) {
          array_push($warnings, $mlText[33] . ' ' . $row . ' ' . $mlText[34]);
        }
      }

      if ($previousRow > -1) {
        $strTable .= "</tr>\n</table>";
      }

      $strTable .= "\n<table width='100%' border='0' cellpadding='2' cellspacing='2'><tr>";

      $strTable .= "<td align='center' valign='middle' style='padding:0px; width:10px;'>"
        . "<div>$strSwapRowContainersWithPrevious</div><div>$strSwapRowContainersWithNext</div>"
        . "</td>";
    }

    $templateContainerId = $templateContainer->getId();

    // Get the container's elements
    $strListElement = "<table border='0' id='containers' cellpadding='2' cellspacing='2'>";
    if ($templateElements = $templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
      foreach ($templateElements as $templateElement) {
        $templateElementId = $templateElement->getId();
        $elementType = $templateElement->getElementType();
        $objectId = $templateElement->getObjectId();
        $hide = $templateElement->getHide();

        $elementDescription = $templateElementUtils->getDescription($elementType);
        $elementHelp = $templateElementUtils->getHelp($elementType);
        $templateElementsList[$elementType] = $elementDescription;

        $strSwapPreviousElement = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[5]'>"
          . "\n<input type='hidden' name='templateElementId' value='$templateElementId'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='swapPreviousElement' value='1'>"
          . "\n</form>";

        $strSwapNextElement = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[4]'>"
          . "\n<input type='hidden' name='templateElementId' value='$templateElementId'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='swapNextElement' value='1'>"
          . "\n</form>";

        if ($hide) {
          $imageShowHide = $gImageShow;
          $titleShowHide = $mlText[28];
        } else {
          $imageShowHide = $gImageHide;
          $titleShowHide = $mlText[27];
        }

        $strShowHideElement = "\n<form style='margin:0px;' action='$PHP_SELF' method='post' name='hideElement'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$imageShowHide' title='$titleShowHide'>"
          . "\n<input type='hidden' name='templateElementId' value='$templateElementId'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='showHideElement' value='1'>"
          . "\n</form>";

        $strDeleteElement = $gConfirmDelete;
        $strDeleteElement .= "\n<form style='margin:0px;' action='$PHP_SELF' method='post' id='deleteElement' name='deleteElement' onsubmit='javascript:return confirmDelete(this)'>"
          . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'>"
          . "\n<input type='hidden' name='templateElementId' value='$templateElementId'>"
          . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
          . "\n<input type='hidden' name='deleteElement' value='1'>"
          . "\n</form>";

        // Display an edit page if the element can have several object instances
        if ($objectId && $elementType != 'PAGE') {
          $editContentUrl = "$gTemplateUrl/design/element/editContent.php?templateElementId=$templateElementId&elementType=$elementType&objectId=$objectId";
          $strElementContent = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$editContentUrl'><img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[11]'></a>";
        } else {
          $strElementContent = '';
        }

        $strElementDuplicate = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[26]'>", "$gTemplateUrl/design/element/duplicate.php?templateElementId=$templateElementId", 800, 400);

        $strElementProperty = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$gTemplateUrl/design/element/editProperty.php?templateElementId=$templateElementId'><img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[9]'></a>";

        $strPreview = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[25]'>", "$gTemplateUrl/design/element/preview.php?templateModelId=$templateModelId&templateElementId=$templateElementId", 800, 600);

        $strElementDescription = "<img border='0' src='$gCommonImagesUrl/$gImageQuestion' alt=''> $elementDescription";
        $label = $popupUtils->getTipPopup($strElementDescription, $elementHelp, 300, 300);
        $strListElement .= "\n<tr><td valign='top'>$label</td><td valign='top'>$strSwapPreviousElement</td><td valign='top'>$strSwapNextElement</td><td valign='top'>$strElementContent</td><td valign='top'>$strElementProperty</td><td valign='top'>$strPreview</td><td valign='top'>$strElementDuplicate</td><td valign='top'>$strShowHideElement</td><td valign='top'>$strDeleteElement</td></tr>";
      }
    }
    $strListElement .= "\n</table>";

    $strShowAddElementList = "";

    $strAddElement = "<a href='javascript:void(0);' id='addElementImage$templateContainerId' onClick=\"document.getElementById('addElementImage$templateContainerId').style.display='none'; document.getElementById('addElementButton$templateContainerId').style.display='block'; \"><img src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'/></a>"
      . "\n<span id='addElementButton$templateContainerId' style='display:none'>"
      . "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
      . "\n" . $strSelectElements
      . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
      . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
      . "\n<input type='hidden' name='addElement' value='1'>"
      . "\n</form>"
      . "\n</span>";

    $strContainerProperty = "<a href='#' onMouseOver=\"javascript:underlineBorder(document.getElementById('container$templateContainerId'));\" onMouseOut=\"javascript:resetBorder(document.getElementById('container$templateContainerId'));\" onClick=\"popup = dialogPopupNew('$gTemplateUrl/design/container/property.php?templateContainerId=$templateContainerId', '', 200, 100, 700, 700, 1);\" style='white-space: nowrap;' title='$mlText[7]'> <img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[7]'></a>";

    if (!$parentId && !$hasChildren) {
      $strAddContainer = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageAddSmall' title='$mlText[20]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='addContainer' value='1'>"
        . "\n</form>";

      $strSwapNextContainer = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageRight' title='$mlText[4]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
        . "\n<input type='hidden' name='swapNextContainer' value='1'>"
        . "\n</form>";

      $strSwapPreviousContainer = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageLeft' title='$mlText[5]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
        . "\n<input type='hidden' name='swapPreviousContainer' value='1'>"
        . "\n</form>";

      $strMoveToNextRow = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[22]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
        . "\n<input type='hidden' name='moveToNextRow' value='1'>"
        . "\n</form>";

      $strMoveToPreviousRow = "\n<form style='margin:0px;' action='$PHP_SELF' method='post'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[23]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='templateModelId' value='$templateModelId'>"
        . "\n<input type='hidden' name='moveToPreviousRow' value='1'>"
        . "\n</form>";
    } else {
      $strAddContainer = '';
      $strSwapNextContainer = '';
      $strSwapPreviousContainer = '';
      $strMoveToNextRow = '';
      $strMoveToPreviousRow = '';
    }

    if (!$parentId && !$hasChildren && count($templateElements) == 0) {
      $strDeleteContainer = $gConfirmDelete;
      $strDeleteContainer .= "\n<form style='margin:0px;' action='$PHP_SELF' method='post' onsubmit='javascript:return confirmDelete(this)'>"
        . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[19]'>"
        . "\n<input type='hidden' name='templateContainerId' value='$templateContainerId'>"
        . "\n<input type='hidden' name='deleteContainer' value='1'>"
        . "\n</form>";
    } else {
      $strDeleteContainer = '';
    }

    $strPreview = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[24]'>", "$gTemplateUrl/design/container/preview.php?templateModelId=$templateModelId&templateContainerId=$templateContainerId", 800, 600);

    $strContainerWidthHeight = $templateContainerUtils->getWidth($templateContainerId)
      . '*'
      . $templateContainerUtils->getHeight($templateContainerId);

    $strTable .= "<td id='container$templateContainerId' align='center' valign='top' style='padding:0px; border-style:solid; border-width:1px; border-color:#000000;'>"
      . "\n<table width='100%' border='0' cellpadding='2' cellspacing='2'>"
      . "<tr><td valign='top'><table width='1%' border='0' cellpadding='0' cellspacing='0'><tr><td valign='top'>$strMoveToPreviousRow</td><td valign='top'>$strMoveToNextRow</td><td valign='top'>$strSwapPreviousContainer</td><td valign='top'>$strSwapNextContainer</td><td valign='top'>$strDeleteContainer</td><td valign='top'>$strAddContainer</td></tr></table></td><td align='right' valign='top' style='white-space:nowrap;'>$strContainerWidthHeight $strContainerProperty $strPreview</td></tr>"
      . "<tr><td colspan='2' style='padding:10px;' align='right' width='90%' valign='top'>"
      . $strListElement
      . $strAddElement
      . "</td></tr>"
      . "</table>"
      . "</td>";
  }
}

$strTable .=  "</td></tr></table>";

if (!$templateModelUtils->sameContainersWidthUnit($templateModelId)) {
  array_push($warnings, $mlText[17]);
}

if (!$templateModelUtils->sameContainerRowsWidth($templateModelId)) {
  array_push($warnings, $mlText[32]);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$help = $popupUtils->getHelpPopup($mlText[8], 300, 400);
$panelUtils->setHelp($help);

$preferenceUtils->init($templateUtils->preferences);
$strCacheRequested = $templateUtils->renderCacheRequestJs();
$strCacheRequested .= ' ' . $templateUtils->renderCacheRequestMessage($templateModelId);

$parentLink = '';
if ($parentId) {
  if ($parentTemplateModel = $templateModelUtils->selectById($parentId)) {
    $parentName = $parentTemplateModel->getName();
    $parentLink = "($mlText[31] <a href='$gTemplateUrl/design/model/compose.php?templateModelId=$parentId' $gJSNoStatus>$parentName</a>)";
  }
}

$panelUtils->addLine($panelUtils->addCell("<b>$mlText[6]</b> <span title='$description'>$name</span> $parentLink", "n"));
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));

$strPreview = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[12]'> $mlText[12]", "$gTemplateUrl/design/model/preview.php?templateModelId=$templateModelId", 800, 600);

$strCommand = "<a href='$gTemplateUrl/design/page/admin.php?templateModelId=$templateModelId' $gJSNoStatus>" . "$mlText[14] <img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[14]'></a>";

$panelUtils->addLine($panelUtils->addCell($strPreview, "nb"), '', $panelUtils->addCell($strCommand, "nr"));

$strModelProperty = "<a href='#' onMouseOver=\"javascript:underlineBorder(document.getElementById('bodyTable'));\" onMouseOut=\"javascript:document.getElementById('bodyTable').style.border = '';\" onClick=\"popup = dialogPopupNew('$gTemplateUrl/design/model/property.php?templateModelId=$templateModelId', '', 200, 100, 700, 700, 1);\" title='$mlText[10]'>$mlText[10] <img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[10]'></a>";

$panelUtils->addLine($panelUtils->addCell($strCacheRequested, ''), $panelUtils->addCell($strModelProperty, "r"));

$panelUtils->addLine($panelUtils->addCell($strTable, "c"));
$panelUtils->addContent($strJavascript);

$str = $panelUtils->render();

printAdminPage($str);

?>
