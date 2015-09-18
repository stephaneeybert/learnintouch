<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$mlText = $languageUtils->getMlText(__FILE__);

$categoryId = LibEnv::getEnvHttpPOST("categoryId");

if (!$categoryId) {
  $categoryId = LibSession::getSessionValue(PEOPLE_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(PEOPLE_SESSION_CATEGORY, $categoryId);
}

$categories = $peopleCategoryUtils->selectAll();
$catList = Array('-1' => '');
foreach ($categories as $category) {
  $wCatId = $category->getId();
  $wName = $category->getName();
  $catList[$wCatId] = $wName;
}

$strSelect = LibHtml::getSelectList("categoryId", $catList, $categoryId, true);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$strCommand = "<a href='$gPeopleUrl/category/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[6]'></a>"
. " <a href='$gPeopleUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[7]'></a>";

$help = $popupUtils->getHelpPopup($mlText[12], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $panelUtils->addCell($strSelect, "n"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->closeForm();
$panelUtils->addLine();
$strCommand = "<a href='$gPeopleUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$peoples = $peopleUtils->selectByCategoryId($categoryId);

$sortableLinesClass = 'sortableLines_' . $categoryId;
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
      $.post('$gPeopleUrl/list_order.php', {'peopleIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($peoples as $people) {
  $peopleId = $people->getId();

  $firstname = $people->getFirstname();
  $lastname = $people->getLastname();
  $email = $peopleUtils->renderEmail($people);

  if ($categoryId) {
    $strSortable = "<span class='sortableItem' sortableItemId='$peopleId'></span>";

    $strSwap = "<a href='$gPeopleUrl/swapup.php?peopleId=$peopleId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
      . " <a href='$gPeopleUrl/swapdown.php?peopleId=$peopleId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";
  } else {
    $strSortable = '';
    $strSwap = '';
  }

  $strCommand = "<a href='$gPeopleUrl/edit.php?peopleId=$peopleId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[4]'>", "$gPeopleUrl/image.php?peopleId=$peopleId", 600, 600)
    . " <a href='$gPeopleUrl/delete.php?peopleId=$peopleId' $gJSNoStatus>" . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($panelUtils->addCell("$strSortable $strSwap $firstname $lastname", "n"), $panelUtils->addCell($email, "n"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("people_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
