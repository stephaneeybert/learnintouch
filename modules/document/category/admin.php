<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gDocumentUrl/admin.php");
$strCommand = "<a href='$gDocumentUrl/category/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$categories = $documentCategoryUtils->selectAll();

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
      $.post('$gDocumentUrl/category/list_order.php', {'documentCategoryIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($categories as $category) {
  $categoryId = $category->getId();
  $name = $category->getName();
  $description = $category->getDescription();

  $strSortable = "<span class='sortableItem' sortableItemId='$categoryId'></span>";

  $strSwap = "<a href='$gDocumentUrl/category/swapup.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp'title='$mlText[11]'></a> <a href='$gDocumentUrl/category/swapdown.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gDocumentUrl/category/edit.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gDocumentUrl/category/delete.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
