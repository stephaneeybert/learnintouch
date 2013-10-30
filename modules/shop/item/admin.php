<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$shopCategoryId = LibEnv::getEnvHttpPOST("shopCategoryId");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(SHOP_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(SHOP_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$shopCategoryId) {
  $shopCategoryId = LibSession::getSessionValue(SHOP_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(SHOP_SESSION_CATEGORY, $shopCategoryId);
}

if ($searchPattern) {
  $shopCategoryId = '';
  LibSession::putSessionValue(SHOP_SESSION_CATEGORY, '');
}

$searchPattern = LibString::cleanString($searchPattern);

$categories = $shopCategoryUtils->getCategories();
$categoryList = Array('-1' => '');
foreach ($categories as $category) {
  $wShopCategoryId = $category[0];
  $level = $category[1];
  if ($shopCategory = $shopCategoryUtils->selectById($wShopCategoryId)) {
    $wShopCategoryId = $shopCategory->getId();
    $wName = $shopCategory->getName();
    $wName = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level) . " " . $wName;
    $categoryList[$wShopCategoryId] = $wName;
  }
}
$strSelectCategory = LibHtml::getSelectList("shopCategoryId", $categoryList, $shopCategoryId, true);

$panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");
$panelUtils->openForm($PHP_SELF);
$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$help = $popupUtils->getHelpPopup($mlText[13], 300, 400);
$panelUtils->setHelp($help);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . $panelUtils->getTinyOk()
  . "</form>";

$strCommand = "<a href='$gShopUrl/order/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageShopOrder' title='$mlText[16]'></a>"
  . " <a href='$gShopUrl/category/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[6]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', $panelUtils->addCell($strCommand, "nr"));

$strSelectCategory = "<form action='$PHP_SELF' method='post'>"
  . "$strSelectCategory "
  . "</form>";

$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strSelectCategory, "n"), '', '');

$strCommand = "<a href='$gShopUrl/item/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[12]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), $panelUtils->addCell($strCommand, "nr"));

$sortableLinesClass = 'sortableLines_' . $shopCategoryId;

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
      $.post('$gShopUrl/item/list_order.php', {'shopItemIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$preferenceUtils->init($shopItemUtils->preferences);
$listStep = $preferenceUtils->getValue("SHOP_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $shopItems = $shopItemUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $shopItems = $shopItemUtils->selectByCategoryId($shopCategoryId, $listIndex, $listStep);
}

$listNbItems = $shopItemUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList($sortableLinesClass);
foreach ($shopItems as $shopItem) {
  $shopItemId = $shopItem->getId();
  $name = $shopItem->getName();
  $shortDescription = $shopItem->getShortDescription();
  $url = $shopItem->getUrl();

  if (!$name) {
    $name = LibString::wordSubtract($shortDescription, 6);
  }

  $strName = $popupUtils->getDialogPopup($name, "$gShopUrl/display.php?shopItemId=$shopItemId", 600, 600);

  $strSortable = "<span class='sortableItem' sortableItemId='$shopItemId'></span>";

  $strSwap = "<a href='$gShopUrl/item/swapup.php?shopItemId=$shopItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
    . " <a href='$gShopUrl/item/swapdown.php?shopItemId=$shopItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gShopUrl/item/edit.php?shopItemId=$shopItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gShopUrl/item/image/admin.php?shopItemId=$shopItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'></a>"
    . " <a href='$gShopUrl/item/delete.php?shopItemId=$shopItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strSortable . ' ' . $strSwap . ' ' . $strName, '', $url, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("shop_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
