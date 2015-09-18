<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$categoryId = LibEnv::getEnvHttpPOST("categoryId");

if (!$categoryId) {
  $categoryId = LibSession::getSessionValue(NAVLINK_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(NAVLINK_SESSION_CATEGORY, $categoryId);
}

$categories = $linkCategoryUtils->selectAll();
$linkCategoryList = Array('-1' => '');
foreach ($categories as $linkCategory) {
  $wLinkCategoryId = $linkCategory->getId();
  $wName = $linkCategory->getName();
  $linkCategoryList[$wLinkCategoryId] = $wName;
}
$strSelect = LibHtml::getSelectList("categoryId", $linkCategoryList, $categoryId, true);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$strCommand = "<a href='$gLinkUrl/category/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[6]'></a>"
  . " <a href='$gLinkUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";

$help = $popupUtils->getHelpPopup($mlText[13], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strSelect, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeForm();
$panelUtils->addLine();
$strCommand = "<a href='$gLinkUrl/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[12], "nb"), $panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

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
      $.post('$gLinkUrl/list_order.php', {'linkIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$links = $linkUtils->selectByCategoryId($categoryId);

$panelUtils->openList($sortableLinesClass);
foreach ($links as $link) {
  $linkId = $link->getId();
  $name = $link->getName();
  $image = $link->getImage();
  $url = $link->getUrl();

  if ($image) {
    $strImage = "<img src='" . $linkUtils->imageFileUrl . '/' . $image . "' border='0' href='' title='$image'>";
  } else {
    $strImage = '';
  }

  if ($categoryId) {
    $strSortable = "<span class='sortableItem' sortableItemId='$linkId'></span>";

    $strSwap = "<a href='$gLinkUrl/swapup.php?linkId=$linkId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
      . " <a href='$gLinkUrl/swapdown.php?linkId=$linkId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";
  } else {
    $strSortable = '';
    $strSwap = '';
  }

  $strCommand = "<a href='$gLinkUrl/edit.php?linkId=$linkId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gLinkUrl/image.php?linkId=$linkId", 600, 600)
    . " <a href='$gLinkUrl/delete.php?linkId=$linkId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strSortable . ' ' . $strSwap . ' ' . $name, $strImage, $url, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("link_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
