<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gContactUrl/status/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$contactStatuses = $contactStatusUtils->selectAll();

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
      $.post('$gContactUrl/status/list_order.php', {'contactStatusIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($contactStatuses as $contactStatus) {
  $contactStatusId = $contactStatus->getId();
  $name = $contactStatus->getName();
  $description = $contactStatus->getDescription();

  $strSortable = "<span class='sortableItem' sortableItemId='$contactStatusId'></span>";

  $strSwap = "<a href='$gContactUrl/status/swapup.php?contactStatusId=$contactStatusId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
    . " <a href='$gContactUrl/status/swapdown.php?contactStatusId=$contactStatusId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gContactUrl/status/edit.php?contactStatusId=$contactStatusId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gContactUrl/status/delete.php?contactStatusId=$contactStatusId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
