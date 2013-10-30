<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $subject = LibEnv::getEnvHttpPOST("subject");

  if ($mailUtils->isLockedForLoggedInAdmin($mailId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $mailUtils->deleteMail($mailId);

    $str = LibHtml::urlRedirect("$gMailUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $mailId = LibEnv::getEnvHttpGET("mailId");

  $subject = '';
  if ($mail = $mailUtils->selectById($mailId)) {
    $subject = $mailUtils->renderSubject($mail);
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $subject);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailId', $mailId);
$panelUtils->addHiddenField('subject', $subject);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
