<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gLinkUrl/admin.php");
$strCommand = "<a href='$gLinkUrl/category/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$linkCategories = $linkCategoryUtils->selectAll();

$sortableLinesClass = 'sortableLines';
$strSortableLines = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $("tbody .$sortableLinesClass").sortable({
    cursor: 'move',
    update: function(ev, ui) {
      var sortableItemIds = [];
      $("tbody .$sortableLinesClass .sortableItem").each(function(index){
        var sortableItemId = $(this).attr("sortableItemId");
        sortableItemIds.push(sortableItemId);
      });
      $.post('$gLinkUrl/category/list_order.php', {'linkCategoryIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($linkCategories as $linkCategory) {
  $categoryId = $linkCategory->getId();
  $name = $linkCategory->getName();
  $description = $linkCategory->getDescription();

  $strSortable = "<span class='sortableItem' sortableItemId='$categoryId'></span>";

  $strSwap = "<a href='$gLinkUrl/category/swapup.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp'title='$mlText[11]'></a> <a href='$gLinkUrl/category/swapdown.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gLinkUrl/category/edit.php?linkCategoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gLinkUrl/category/delete.php?linkCategoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
