<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gPeopleUrl/admin.php");
$strCommand = "<a href='$gPeopleUrl/category/edit.php' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$peopleCategories = $peopleCategoryUtils->selectAll();

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
      $.post('$gPeopleUrl/category/list_order.php', {'peopleCategoryIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($peopleCategories as $peopleCategory) {
  $categoryId = $peopleCategory->getId();
  $name = $peopleCategory->getName();
  $description = $peopleCategory->getDescription();

    $strSortable = "<span class='sortableItem' sortableItemId='$categoryId'></span>";

    $strSwap = "<a href='$gPeopleUrl/category/swapup.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp'title='$mlText[11]'></a> <a href='$gPeopleUrl/category/swapdown.php?categoryId=$categoryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gPeopleUrl/category/edit.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gPeopleUrl/category/delete.php?categoryId=$categoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
