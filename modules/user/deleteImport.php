<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $userUtils->deleteLastImportedUsers();

  $str = LibHtml::urlRedirect("$gUserUrl/admin.php");
  printContent($str);
  return;

} else {

  $nbImported = $userUtils->countImported();

  if ($nbImported == 0) {
    array_push($warnings, $mlText[4]);
  }

  $strWarning = '';
  if (count($warnings) > 0) {
    foreach ($warnings as $warning) {
      $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  if ($nbImported > 0) {
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell("$mlText[2]", "br"), $nbImported);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell("$mlText[1]", "br"), $panelUtils->getOk());
    $panelUtils->addHiddenField('formSubmitted', 1);
    $panelUtils->closeForm();
  }

  if ($users = $userUtils->selectImported()) {
    $panelUtils->addLine();
    $panelUtils->addLine('', $panelUtils->addCell("$mlText[3]", "nb"));
    $panelUtils->addLine();
    foreach($users as $user) {
      $userId = $user->getId();
      $email = $user->getEmail();
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $panelUtils->addLine('', $panelUtils->addCell("$email $firstname $lastname", ""));
    }
  }

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
