<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $languageCode = LibEnv::getEnvHttpPOST("languageCode");

  // Create the file name
  $filename = $backupUtils->backupFilePath . "language_" . $languageCode . "_" . date("Y-m-d") . "_" . date("H-i") . ".tar.gz";

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

  $str = LibHtml::urlRedirect("$gBackupUrl/admin.php");
  printMessage($str);
  return;

} else {

  $panelUtils->setHeader($mlText[0], "$gBackupUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[1], 200, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();

  $list = array("en" => "English", "fr" => "Français", "se" => "Svenska");
  $strSelectLanguages = LibHtml::getSelectList("languageCode", $list);

  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectLanguages);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[15], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
