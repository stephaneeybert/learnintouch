<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $subject = LibEnv::getEnvHttpPOST("subject");
  $description = LibEnv::getEnvHttpPOST("description");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  $description = LibString::cleanString($description);

  // The subject is required
  if (!$subject) {
    array_push($warnings, $mlText[8]);
  }

  if ($mailUtils->isLockedForLoggedInAdmin($mailId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    if ($mail = $mailUtils->selectById($mailId)) {
      $mail->setSubject($subject);
      $mail->setDescription($description);
      $mail->setCategoryId($categoryId);
      $mailUtils->update($mail);
    }

    $str = LibHtml::urlRedirect("$gMailUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $mailId = LibEnv::getEnvHttpGET("mailId");

  $subject = '';
  $description = '';
  $categoryId = '';
  if ($mail = $mailUtils->selectById($mailId)) {
    $subject = $mailUtils->renderSubject($mail);
    $subject = LibString::cleanString($subject);
    $description = $mail->getDescription();
    $categoryId = $mail->getCategoryId();
  }

}

$mailCategories = $mailCategoryUtils->selectAll();
$listCategories = Array('' => '');
foreach ($mailCategories as $mailCategory) {
  $wId = $mailCategory->getId();
  $wName = $mailCategory->getName();
  $listCategories[$wId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("categoryId", $listCategories, $categoryId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $strSelectCategory);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='subject' value='$subject' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailId', $mailId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
