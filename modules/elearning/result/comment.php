<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
  $comment = LibEnv::getEnvHttpPOST("comment");
  $hideComment = LibEnv::getEnvHttpPOST("hideComment");
  $sendComment = LibEnv::getEnvHttpPOST("sendComment");

  $elearningResultId = LibString::cleanString($elearningResultId);
  $comment = LibString::cleanString($comment);
  $hideComment = LibString::cleanString($hideComment);
  $sendComment = LibString::cleanString($sendComment);

  if (count($warnings) == 0) {

    if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
      $elearningResult->setComment($comment);
      $elearningResult->setHideComment($hideComment);
      $elearningResultUtils->update($elearningResult);

      if ($sendComment) {
        $elearningResultUtils->sendCommentToParticipant($elearningResult);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/result/view.php?elearningResultId=$elearningResultId");
    printContent($str);
    return;

  }

} else {

  $elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

  $comment = '';
  $hideComment = '';
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $comment = $elearningResult->getComment();
    $hideComment = $elearningResult->getHideComment();
  }

}

if ($hideComment == '1') {
  $checkedHideComment = "CHECKED";
}  else {
  $checkedHideComment = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[4], $mlText[6], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='comment' cols='28' rows='8'>$comment</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideComment' $checkedHideComment value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='sendComment' value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningResultId', $elearningResultId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
