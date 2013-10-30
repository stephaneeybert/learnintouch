<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $linkId = LibEnv::getEnvHttpPOST("linkId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");
  $url = LibEnv::getEnvHttpPOST("url");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $url = LibString::cleanString($url);

  // Validate the url
  if ($url && LibUtils::isInvalidUrl($url)) {
    array_push($warnings, $mlText[21]);
  }

  // Format the url
  if ($url) {
    $url = LibUtils::formatUrl($url);
  }

  // If the link is assigned to another category then the link list order
  // must be set according to the category number of links
  // Otherwise the link list order is not changed

  $listOrder = '';
  if ($link = $linkUtils->selectById($linkId)) {
    $listOrder = $link->getListOrder();
    $currentCategoryId = $link->getCategoryId();
  } else {
    $currentCategoryId = '';
  }

  // It must be a zero and not an empty value otherwise the list order will be reassigned every time
  if (!$currentCategoryId) {
    $currentCategoryId = '0';
  }

  // Check if the category has changed
  if ($currentCategoryId != $categoryId) {
    // Get the next list order
    $listOrder = $linkUtils->getNextListOrder($categoryId);
  }

  if (count($warnings) == 0) {

    if ($link = $linkUtils->selectById($linkId)) {
      $link->setName($name);
      $link->setDescription($description);
      $link->setUrl($url);
      $link->setCategoryId($categoryId);
      $link->setListOrder($listOrder);
      $linkUtils->update($link);
    } else {
      $link = new Link();
      $link->setName($name);
      $link->setDescription($description);
      $link->setUrl($url);
      $link->setCategoryId($categoryId);
      $link->setListOrder($listOrder);
      $linkUtils->insert($link);
    }

    $str = LibHtml::urlRedirect("$gLinkUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $linkId = LibEnv::getEnvHttpGET("linkId");

  $name = '';
  $description = '';
  $url = '';
  $categoryId = '';
  if ($linkId) {
    if ($link = $linkUtils->selectById($linkId)) {
      $name = $link->getName();
      $description = $link->getDescription();
      $url = $link->getUrl();
      $categoryId = $link->getCategoryId();
    }
  } else {
    $categoryId = LibSession::getSessionValue(NAVLINK_SESSION_CATEGORY);
  }

}

$linkCats = $linkCategoryUtils->selectAll();
$linkCatList = Array('' => '');
foreach ($linkCats as $linkCat) {
  $wLinkCatId = $linkCat->getId();
  $wName = $linkCat->getName();
  $linkCatList[$wLinkCatId] = $wName;
}
$strSelect = LibHtml::getSelectList("categoryId", $linkCatList, $categoryId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gLinkUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='url' value='$url' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('linkId', $linkId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
