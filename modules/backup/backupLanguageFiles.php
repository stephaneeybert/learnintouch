<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

$mlText = $languageUtils->getMlText(__FILE__);

$login = $adminUtils->getSessionLogin();
if ($admin = $adminUtils->selectByLogin($login)) {
  $adminId = $admin->getId();
  $value = $adminOptionUtils->getOptionValue(OPTION_LANGUAGE_TRANSLATE, $adminId);
  $values = $websiteOptionUtils->getOptionValues(OPTION_LANGUAGE_TRANSLATE);
  $languageCode = $values[$value];
} else if ($adminUtils->isStaff()) {
  $languageCode = $languageUtils->getCurrentLanguageCode();
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Create the file name
  $filename = $backupUtils->exportFilePath . "language_" . $languageCode . ".tar";

  // Backup the files for the language
  $backupSuccess = $backupUtils->backupLanguageFiles($filename, $languageCode);

  // Check for the backup success
  if ($backupSuccess == false) {
    $webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
    $webmasterName = $profileUtils->getProfileValue("webmaster.name");

    if (!$webmasterName) {
      $webmasterName = $webmasterEmail;
    }

    $strSubject = $mlText[8];
    $strBody = "$mlText[9] <a href='mailto:$webmasterEmail'>$webmasterName</a>";

    if ($webmasterEmail) {
      LibEmail::sendMail($webmasterEmail, $webmasterName, $strSubject, $strBody, $webmasterEmail, $webmasterName);
    }

    $staffEmail = $adminUtils->staffEmail;
    if ($staffEmail) {
      LibEmail::sendMail($staffEmail, $staffEmail, $strSubject, $strBody, $webmasterEmail, $webmasterName);
    }

    $str = $mlText[8];
    $str .= LibHtml::urlDisplayRedirect("$gBackupUrl/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
  }

  // Display a popup window to save the file on the local computer
  if ($filename) {
    LibFile::downloadFile($filename);
  }

  $str = LibHtml::urlRedirect("$gBackupUrl/admin.php");
  printMessage($str);
  return;

} else {

  $panelUtils->setHeader($mlText[0], "$gBackupUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[1], 200, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();

  if ($language = $languageUtils->selectByCode($languageCode)) {
    $languageId = $language->getId();
    $name = $language->getName();
    $strImage = $languageUtils->renderImage($languageId);
    $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "$strImage $name");
    $panelUtils->addLine();
  }

  $panelUtils->addLine($panelUtils->addCell($mlText[15], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
