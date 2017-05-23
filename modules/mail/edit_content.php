<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $body = LibEnv::getEnvHttpPOST("body");
  $subject = LibEnv::getEnvHttpPOST("subject");

  if ($mailUtils->isLockedForLoggedInAdmin($mailId)) {
    array_push($warnings, $mlText[3]);
  }

  // Fix the double escaping of & if any
  // I don't know how these happen..
  if (strstr($body, "&amp;amp;")) {
    $body = str_replace("&amp;amp;", "&amp;", $body);
  }

  // Fix incorrect email address mailto urls
  if (strstr($body, "mailto")) {
    $body = $mailUtils->fixMailtoUrls($body);
  }

  if (count($warnings) == 0) {

    if ($mail = $mailUtils->selectById($mailId)) {
      $systemDateTime = $clockUtils->getSystemDateTime();

      $mail->setBody($body);
      $mail->setCreationDate($systemDateTime);
      $mailUtils->update($mail);
    }

    $str = LibHtml::urlRedirect("$gMailUrl/admin.php");
    printContent($str);
    exit;

  }

} else {

  $mailId = LibEnv::getEnvHttpGET("mailId");

  $subject = '';
  $body = '';
  if ($mailId) {
    if ($mail = $mailUtils->selectById($mailId)) {
      $subject = $mail->getSubject();
      $body = $mail->getBody();
    }
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
$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->addLine("<b>$mlText[6]</b> $subject", '');
$panelUtils->openForm($PHP_SELF);

include($gHtmlEditorPath . "CKEditorUtils.php");
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->setImagePath($mailUtils->imagePath);
$contentEditor->setImageUrl($mailUtils->imageUrl);
$contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_mail.php');
$contentEditor->withStandardToolbar();
$contentEditor->withImageButton();
$contentEditor->withMetaNames();
$contentEditor->setMetaNamesJs($mailUtils->renderMetaNamesJs());
$contentEditor->setHeight(500);
$str = $contentEditor->render();
$str .= $contentEditor->renderInstance("body", $body);
$panelUtils->addLine($str);

$panelUtils->addHiddenField('mailId', $mailId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('subject', $subject);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
