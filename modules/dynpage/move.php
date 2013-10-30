<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");
  $parentId = LibEnv::getEnvHttpPOST("parentId");

  $dynpage = $dynpageUtils->selectById($dynpageId);

  if ($parentId != $dynpage->getParentId() && !$dynpageUtils->isGrandChildOf($parentId, $dynpageId)) {
    if ($parentId == DYNPAGE_ROOT_ID) {
      $parentId = 0;
    }

    $dynpage->setParentId($parentId);
    $dynpage->setListOrder($dynpageUtils->getNextListOrder($parentId));
    $dynpageUtils->update($dynpage);

    $str = LibJavascript::reloadParentWindow();
    printContent($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

} else {

  $movedDynpageId = LibEnv::getEnvHttpGET("dynpageId");

}

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);

$folderPath = $dynpageUtils->getFolderPath($movedDynpageId);

$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $folderPath);
$panelUtils->addLine();

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine("<input type='image' border='0' src='$gCommonImagesUrl/$gImageFolder' title='$mlText[3]'> /", '');
$panelUtils->addHiddenField('parentId', '');
$panelUtils->addHiddenField('dynpageId', $movedDynpageId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$dynpages = renderChildren(DYNPAGE_ROOT_ID, 0);

$str = $panelUtils->render();

printAdminPage($str);

function renderChildren($parentId, $indentLevel) {
  global $dynpageUtils;
  global $panelUtils;
  global $gImagePage;
  global $gImageFolder;
  global $PHP_SELF;
  global $mlText;
  global $gCommonImagesUrl;
  global $movedDynpageId;

  $strIndent = str_repeat('&nbsp;', $indentLevel * 8);
  $indentLevel++;

  $dynpages = $dynpageUtils->selectChildren($parentId);

  foreach ($dynpages as $dynpage) {
    $dynpageId = $dynpage->getId();
    $name = $dynpage->getName();

    $hasChild = $dynpageUtils->hasChild($dynpageId);

    if ($hasChild) {
      $imageFolder = $gImageFolder;
    } else {
      $imageFolder = $gImagePage;
    }

    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine("$strIndent <input type='image' border='0' src='$gCommonImagesUrl/$imageFolder' title='$mlText[2]'> $name");
    $panelUtils->addHiddenField('parentId', $dynpageId);
    $panelUtils->addHiddenField('dynpageId', $movedDynpageId);
    $panelUtils->addHiddenField('formSubmitted', 1);
    $panelUtils->closeForm();

    if ($dynpageUtils->hasChild($dynpageId)) {
      renderChildren($dynpageId, $indentLevel);
    }
  }
}

?>
