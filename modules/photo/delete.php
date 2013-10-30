<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoId = LibEnv::getEnvHttpPOST("photoId");

  $photoUtils->deletePhoto($photoId);

  $str = LibHtml::urlRedirect("$gPhotoUrl/admin.php");
  printContent($str);
  return;

} else {

  $photoId = LibEnv::getEnvHttpGET("photoId");

  if ($photo = $photoUtils->selectById($photoId)) {
    $image = $photo->getImage();
    $reference = $photo->getReference();
    $description = $photo->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gPhotoUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $reference);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('photoId', $photoId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
