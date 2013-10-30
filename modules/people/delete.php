<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $peopleId = LibEnv::getEnvHttpPOST("peopleId");

  $peopleUtils->deletePerson($peopleId);

  $str = LibHtml::urlRedirect("$gPeopleUrl/admin.php");
  printContent($str);
  return;

} else {

  $peopleId = LibEnv::getEnvHttpGET("peopleId");

  if ($people = $peopleUtils->selectById($peopleId)) {
    $firstname = $people->getFirstname();
    $lastname = $people->getLastname();
    $email = $people->getEmail();
  }

  $panelUtils->setHeader($mlText[0], "$gPeopleUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "$firstname $lastname");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $email);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('peopleId', $peopleId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
