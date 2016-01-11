<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");

$help = $popupUtils->getHelpPopup($mlText[9], 300, 500);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk();

$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[33]'>", "$gDynpageUrl/image.php", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageTree' title='$mlText[35]'>", "$gDynpageUrl/tree.php", 600, 600);
$moduleSecuredPages = $adminModuleUtils->moduleGrantedToAdmin(MODULE_SECURED_PAGES);
if ($moduleSecuredPages) {
  $strCommand .= " <a href='$gDynpageUrl/secureAdmin.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageLock' title='$mlText[39]'></a>";
}
$strCommand .= " <a href='$gDynpageUrl/garbage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageGarbage' title='$mlText[32]'></a>"
  . " <a href='$gDynpageUrl/entry.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageHome' title='$mlText[40]'></a>"
  . " <a href='$gDynpageUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[27]'></a>";

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeForm();
$panelUtils->addLine();

$strCommand = " <a href='$gDynpageUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$isSuperAdmin = $adminUtils->isLoggedSuperAdmin();

if ($searchPattern) {

  $dynpages = $dynpageUtils->selectLikePattern($searchPattern);

  $panelUtils->openList();
  foreach ($dynpages as $dynpage) {
    $dynpageId = $dynpage->getId();
    $name = $dynpage->getName();
    $parentId = $dynpage->getParentId();
    $path = $dynpageUtils->getFolderPath($dynpageId);
    $panelUtils->addLine($panelUtils->addCell($path, "n"), '', '');
  }
  $panelUtils->closeList();

} else {

  $panelUtils->openList();
  renderChildren(DYNPAGE_ROOT_ID, 0);
  $panelUtils->closeList();

}

$strRememberScroll = LibJavaScript::rememberScroll("dynpage_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.drag_and_drop_page {
  cursor:pointer;
}
.droppable-hover {
  outline:2px solid #ABABAB;
}
#droppableTooltip {
  position:absolute;
  z-index:9999;
  background-color:#fff;
  font-weight:normal;
  border:1px solid #ABABAB;
  padding:4px;
}
</style>

<script type="text/javascript">

$(document).ready(function() {

  $(".drag_and_drop_page").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop_page").droppable({
    accept: '.drag_and_drop_page', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[7]</div>');   
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      moveInto(ui.draggable.attr("dynpageId"), $(this).attr("dynpageId"));
    }
  });

  $(".move_before_page").droppable({
    accept: '.drag_and_drop_page', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[10]</div>');   
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      moveBefore(ui.draggable.attr("dynpageId"), $(this).attr("dynpageId"));
    }
  });

  $(".move_after_page").droppable({
    accept: '.drag_and_drop_page', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[8]</div>');   
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      moveAfter(ui.draggable.attr("dynpageId"), $(this).attr("dynpageId"));
    }
  });

  function moveInto(dynpageId, parentId) {
    var url = '$gDynpageUrl/move_into.php?dynpageId='+dynpageId+'&parentId='+parentId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveAfter(dynpageId, targetId) {
    var url = '$gDynpageUrl/move_after.php?dynpageId='+dynpageId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveBefore(dynpageId, targetId) {
    var url = '$gDynpageUrl/move_before.php?dynpageId='+dynpageId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function renderPage(responseText) {
    var response = eval('(' + responseText + ')');
    var moved = response.moved;
    if (moved) {
      window.location = window.location.href;
    }
  }

});

</script>
HEREDOC;
$panelUtils->addContent($strListOrderDragAndDrop);

$str = $panelUtils->render();

$strSkipFormFocus = <<<HEREDOC
<script type='text/javascript'>
var skipFormFocus = true;
</script>
HEREDOC;

printAdminPage($str, $strSkipFormFocus);

function renderChildren($currentPageId, $indentLevel) {
  global $mlText;
  global $adminModuleUtils;
  global $dynpageUtils;
  global $templateUtils;
  global $panelUtils;
  global $gImageLock;
  global $gImageAdd;
  global $gDynpageUrl;
  global $gJSNoStatus;
  global $gCommonImagesUrl;
  global $gImageEdit;
  global $isSuperAdmin;
  global $gImagePerson;
  global $gImageMove;
  global $gImageUnlock;
  global $gImageDelete;
  global $gImagePage;
  global $gImageSecuredFolder;
  global $gImageFolder;
  global $gImageCopy;
  global $gImagePreview;
  global $gImagePrinter;
  global $gImageWeb;
  global $gImageUp;
  global $gImageDown;
  global $gImageCompose;
  global $popupUtils;
  global $moduleSecuredPages;
  global $gImageCollapsed;
  global $gImageFolded;
  global $PHP_SELF;

  $strIndent = str_repeat('&nbsp;', $indentLevel * 8);
  $indentLevel++;

  $dynpages = $dynpageUtils->selectChildren($currentPageId);

  foreach ($dynpages as $dynpage) {
    $dynpageId = $dynpage->getId();
    $name = $dynpage->getName();

    $hasChild = $dynpageUtils->hasChild($dynpageId);

    $strPopupPreview = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[18]'>", "$gDynpageUrl/preview.php?dynpageId=$dynpageId", 600, 600);

    $strPopupPrint = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[43]'>", "$gDynpageUrl/print.php?dynpageId=$dynpageId", 600, 600);

    $url = $templateUtils->renderPageUrl($dynpageId);
    $strPopupUrl = $popupUtils->getPopup("<img border='0' src='$gCommonImagesUrl/$gImageWeb' title='$mlText[19]'> ", "$mlText[20] <br><br>" . $url . "<br><br>$mlText[21]", 800, 160);

    $strSwap = "<span class='move_before_page' dynpageId='$dynpageId'>&nbsp;<a href='$gDynpageUrl/swapup.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]'></a></span>"
      . " <span class='move_after_page' dynpageId='$dynpageId'>&nbsp;<a href='$gDynpageUrl/swapdown.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'></a></span>";

    $strCommand = "<a href='$gDynpageUrl/edit.php?parentId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
      . " <a href='$gDynpageUrl/edit.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[11]'></a>"
      . " <a href='$gDynpageUrl/edit_content.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[2]'></a>";

    if ($moduleSecuredPages) {
      if ($dynpage->getSecured()) {
        $mlTextSecure = $mlText[37];
        $imageSecure = $gImageUnlock;
        $secure = 0;
      } else {
        $mlTextSecure = $mlText[36];
        $imageSecure = $gImageLock;
        $secure = 1;
      }
      $strCommand .= " <a href='$gDynpageUrl/secure.php?dynpageId=$dynpageId&secure=$secure' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$imageSecure' title='$mlTextSecure'></a>";
    }

    if ($isSuperAdmin) {
      $strCommand .= " <a href='$gDynpageUrl/assign_admin.php?dynpageId=$dynpageId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImagePerson' title='$mlText[41]'></a>";
    }

    $strCommand .= ' ' . $strPopupPreview . ' ' . $strPopupPrint . ' ' . $strPopupUrl
      . " <a href='$gDynpageUrl/duplicate.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[13]'></a>"
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageMove' title='$mlText[28]'>", "$gDynpageUrl/move.php?dynpageId=$dynpageId", 600, 600)
      . " <a href='$gDynpageUrl/delete.php?dynpageId=$dynpageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

    if ($hasChild) {
      $displayState = LibEnv::getEnvHttpGET("dynpageDisplayState$dynpageId");
      if (!$displayState) {
        $displayState = LibSession::getSessionValue(DYNPAGE_SESSION_DISPLAY . $dynpageId);
      } else {
        LibSession::putSessionValue(DYNPAGE_SESSION_DISPLAY . $dynpageId, $displayState);
      }

      if ($displayState == DYNPAGE_COLLAPSED) {
        $strName = "<a href='$PHP_SELF?dynpageDisplayState$dynpageId=" . DYNPAGE_FOLDED . "' title='$mlText[6]'><img border='0' src='$gCommonImagesUrl/$gImageCollapsed'></a>";
      } else {
        $strName = "<a href='$PHP_SELF?dynpageDisplayState$dynpageId=" . DYNPAGE_COLLAPSED . "' title='$mlText[6]'><img border='0' src='$gCommonImagesUrl/$gImageFolded'></a>";
      }
    } else {
      $strName = "<img border='0' src='$gCommonImagesUrl/$gImagePage'>";
    }

    $strName .= " <span class='drag_and_drop_page' dynpageId='$dynpageId'>" . $name . '</span>';

    $panelUtils->addLine($panelUtils->addCell("$strIndent $strSwap $strName", "n"), '', $panelUtils->addCell("$strCommand", "nr"));

    if ($hasChild) {
      if ($displayState == DYNPAGE_COLLAPSED) {
        renderChildren($dynpageId, $indentLevel);
      }
    }
  }

}

?>
