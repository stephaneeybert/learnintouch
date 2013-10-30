<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsEditorId = LibEnv::getEnvHttpPOST("newsEditorId");
  $adminId = LibEnv::getEnvHttpPOST("adminId");

  // The administrator is required for a new editor only
  if (!$newsEditorId && !$adminId) {
    array_push($warnings, $mlText[4]);
  }

  // Check that the administrator is not already assigned to another editor
  if ($newsEditor = $newsEditorUtils->selectByAdminId($adminId)) {
    $wNewsEditorId = $newsEditor->getId();
    if (!$newsEditorId || ($newsEditorId && $wNewsEditorId && $wNewsEditorId != $newsEditorId)) {
      array_push($warnings, $mlText[1]);
    }
  }

  if (count($warnings) == 0) {

    if (!$newsEditor = $newsEditorUtils->selectById($newsEditorId)) {
      $newsEditor = new NewsEditor();
      $newsEditor->setAdminId($adminId);
      $newsEditorUtils->insert($newsEditor);
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsEditor/admin.php");
    printMessage($str);
    return;

  }

} else {

  $newsEditorId = LibEnv::getEnvHttpGET("newsEditorId");

  $adminId = '';

}

$adminName = '';
if ($newsEditorId) {
  if ($newsEditor = $newsEditorUtils->selectById($newsEditorId)) {
    $adminId = $newsEditor->getAdminId();
  }
}

if ($admin = $adminUtils->selectById($adminId)) {
  $adminName = $admin->getFirstname() . ' ' . $admin->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsEditor/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[3], $mlText[2], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gAdminUrl/suggestAdmins.php", "adminName", "adminId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('adminId', $adminId);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='adminName' value='$adminName' />");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsEditorId', $newsEditorId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
