<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Delete
  $guestbooks = $guestbookUtils->selectAll();

  foreach ($guestbooks as $guestbook) {
    $guestbookId = $guestbook->getId();
    $guestbookUtils->delete($guestbookId);
    }

  $str = LibHtml::urlRedirect("$gGuestbookUrl/admin.php");
  printContent($str);
  return;

  } else {

  $panelUtils->setHeader($mlText[0], "$gGuestbookUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
