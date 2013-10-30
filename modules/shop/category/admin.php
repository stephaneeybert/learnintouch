<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gShopUrl/item/admin.php");
$strCommand = "<a href='$gShopUrl/category/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$categories = $shopCategoryUtils->getCategories();

$panelUtils->openList();
foreach ($categories as $category) {
  $shopCategoryId = $category[0];
  $level = $category[1];

  if (!$shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
    continue;
  }

  $name = $shopCategory->getName();
  $description = $shopCategory->getDescription();

  $strIndent = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level);

  $strName = "<span class='drag_and_drop_category' shopCategoryId='$shopCategoryId'>" . $name . "</span>";

  $strSwap = "<span class='move_before_category' shopCategoryId='$shopCategoryId'>&nbsp;<a href='$gShopUrl/category/swapup.php?shopCategoryId=$shopCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a></span>"
    . " <span class='move_after_category' shopCategoryId='$shopCategoryId'>&nbsp;<a href='$gShopUrl/category/swapdown.php?shopCategoryId=$shopCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a></span>";

  $strCommand = "<a href='$gShopUrl/category/edit.php?parentCategoryId=$shopCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>"
    . " <a href='$gShopUrl/category/edit.php?shopCategoryId=$shopCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gShopUrl/category/delete.php?shopCategoryId=$shopCategoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strIndent $strSwap $strName", $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.drag_and_drop_category {
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

  $(".drag_and_drop_category").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop_category").droppable({
    accept: '.drag_and_drop_category', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[1]</div>');
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      moveInto(ui.draggable.attr("shopCategoryId"), $(this).attr("shopCategoryId"));
    }
  });

  $(".move_before_category").droppable({
    accept: '.drag_and_drop_category', // Specify what kind of element can be dropped
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
      moveBefore(ui.draggable.attr("shopCategoryId"), $(this).attr("shopCategoryId"));
    }
  });

  $(".move_after_category").droppable({
    accept: '.drag_and_drop_category', // Specify what kind of element can be dropped
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
      $(this).children('div#droppableTooltip').remove();
      moveAfter(ui.draggable.attr("shopCategoryId"), $(this).attr("shopCategoryId"));
    }
  });

  function moveInto(shopCategoryId, parentId) {
    var url = '$gShopUrl/category/move_into.php?shopCategoryId='+shopCategoryId+'&parentId='+parentId;
    ajaxAsynchronousRequest(url, renderCategory);
  }

  function moveAfter(shopCategoryId, targetId) {
    var url = '$gShopUrl/category/move_after.php?shopCategoryId='+shopCategoryId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderCategory);
  }

  function moveBefore(shopCategoryId, targetId) {
    var url = '$gShopUrl/category/move_before.php?shopCategoryId='+shopCategoryId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderCategory);
  }

  function renderCategory(responseText) {
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

printAdminPage($str);

?>
