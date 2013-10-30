<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formId = LibEnv::getEnvHttpPOST("formId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $title = LibEnv::getEnvHttpPOST("title");
  $email = LibEnv::getEnvHttpPOST("email");
  $instructions = LibEnv::getEnvHttpPOST("instructions");
  $acknowledge = LibEnv::getEnvHttpPOST("acknowledge");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $mailSubject = LibEnv::getEnvHttpPOST("mailSubject");
  $mailMessage = LibEnv::getEnvHttpPOST("mailMessage");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $title = LibString::cleanString($title);
  $email = LibString::cleanString($email);
  $instructions = LibString::cleanString($instructions);
  $acknowledge = LibString::cleanString($acknowledge);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);
  $mailSubject = LibString::cleanString($mailSubject);
  $mailMessage = LibString::cleanString($mailMessage);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // The email is case insensitive
  $email = strtolower($email);

  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[2]);
  }

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
  }

  if (count($warnings) == 0) {

    if ($form = $formUtils->selectById($formId)) {
      $form->setName($name);
      $form->setDescription($description);
      $form->setTitle($title);
      $form->setTitle($languageUtils->setTextForLanguage($form->getTitle(), $currentLanguageCode, $title));
      $form->setEmail($email);
      $form->setInstructions($languageUtils->setTextForLanguage($form->getInstructions(), $currentLanguageCode, $instructions));
      $form->setAcknowledge($languageUtils->setTextForLanguage($form->getAcknowledge(), $currentLanguageCode, $acknowledge));
      $form->setWebpageId($webpageId);
      $form->setMailSubject($mailSubject);
      $form->setMailMessage($mailMessage);
      $formUtils->update($form);
    } else {
      $form = new Form();
      $form->setName($name);
      $form->setDescription($description);
      $form->setTitle($languageUtils->setTextForLanguage($form->getTitle(), $currentLanguageCode, $title));
      $form->setEmail($email);
      $form->setInstructions($languageUtils->setTextForLanguage('', $currentLanguageCode, $instructions));
      $form->setAcknowledge($languageUtils->setTextForLanguage('', $currentLanguageCode, $acknowledge));
      $form->setWebpageId($webpageId);
      $form->setMailSubject($mailSubject);
      $form->setMailMessage($mailMessage);
      $formUtils->insert($form);
    }

    $str = LibHtml::urlRedirect("$gFormUrl/admin.php");
    printContent($str);
    exit;

  }

} else {

  $formId = LibEnv::getEnvHttpGET("formId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $description = '';
  $title = '';
  $email = '';
  $instructions = '';
  $acknowledge = '';
  $webpageId = '';
  $mailSubject = '';
  $mailMessage = '';
  if ($formId) {
    if ($form = $formUtils->selectById($formId)) {
      $name = $form->getName();
      $description = $form->getDescription();
      $title = $form->getTitle();
      $title = $languageUtils->getTextForLanguage($form->getTitle(), $currentLanguageCode);
      $email = $form->getEmail();
      $instructions = $languageUtils->getTextForLanguage($form->getInstructions(), $currentLanguageCode);
      $acknowledge = $languageUtils->getTextForLanguage($form->getAcknowledge(), $currentLanguageCode);
      $webpageId = $form->getWebpageId();
      $mailSubject = $form->getMailSubject();
      $mailMessage = $form->getMailMessage();
    }
  }

  $webpageName = $templateUtils->getPageName($webpageId);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gFormUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[4], $mlText[3], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[5], $mlText[7], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[8], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 200);
$strField = "<input type='text' id='title' name='title' value='$title' size='30' maxlength='255'>";
$strJsChangeTitle = <<<HEREDOC
<script type='text/javascript'>
function changeTitle(languageCode) {
  var url = '$gFormUrl/getTitle.php?formId=$formId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateTitle);
}
function updateTitle(responseText) {
  var response = eval('(' + responseText + ')');
  var title = response.title;
  document.getElementById('title').value = title;
}
function saveTitle() {
  var title = document.getElementById('title').value;
  title = encodeURIComponent(title);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = {"formId":"$formId", "languageCode":languageCode, "title":title};
  ajaxAsynchronousPOSTRequest("$gFormUrl/updateTitle.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsChangeTitle);
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$strSave = "<a href='javascript:saveTitle();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$mlText[25]' style='margin-top:2px;'></a>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strField . '<br/>' . $strLanguageFlag . ' ' . $strSave);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 200);
$strEditor = "<textarea id='instructions' name='instructions' cols='28' rows='5'>$instructions</textarea>";
$strJsChangeInstructions = <<<HEREDOC
<script type='text/javascript'>
function changeInstructions(languageCode) {
  var url = '$gFormUrl/getInstructions.php?formId=$formId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateInstructions);
}
function updateInstructions(responseText) {
  var response = eval('(' + responseText + ')');
  var instructions = response.instructions;
  document.getElementById('instructions').value = instructions;
}
function saveInstructions() {
  var instructions = document.getElementById('instructions').value;
  instructions = encodeURIComponent(instructions);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = {"formId":"$formId", "languageCode":languageCode, "instructions":instructions};
  ajaxAsynchronousPOSTRequest("$gFormUrl/updateInstructions.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsChangeInstructions);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$strSave = "<a href='javascript:saveInstructions();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$mlText[23]' style='margin-top:2px;'></a>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . '<br/>' . $strLanguageFlag . ' ' . $strSave);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 200);
$strEditor = "<textarea id='acknowledge' name='acknowledge' cols='28' rows='5'>$acknowledge</textarea>";
$strJsChangeAcknowledge = <<<HEREDOC
<script type='text/javascript'>
function changeAcknowledge(languageCode) {
  var url = '$gFormUrl/getAcknowledge.php?formId=$formId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateAcknowledge);
}
function updateAcknowledge(responseText) {
  var response = eval('(' + responseText + ')');
  var acknowledge = response.acknowledge;
  document.getElementById('acknowledge').value = acknowledge;
}
function saveAcknowledge() {
  var acknowledge = document.getElementById('acknowledge').value;
  acknowledge = encodeURIComponent(acknowledge);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = {"formId":"$formId", "languageCode":languageCode, "acknowledge":acknowledge};
  ajaxAsynchronousPOSTRequest("$gFormUrl/updateAcknowledge.php", params);
}
function changeWebsiteLanguage(languageCode) {
  changeTitle(languageCode);
  changeInstructions(languageCode);
  changeAcknowledge(languageCode);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsChangeAcknowledge);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$strSave = "<a href='javascript:saveAcknowledge();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$mlText[24]' style='margin-top:2px;'></a>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . '<br/>' . $strLanguageFlag . ' ' . $strSave);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 200);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[21]'> $mlText[22]", "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='mailSubject' value='$mailSubject' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='mailMessage' cols='28' rows='5'>$mailMessage</textarea>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->addHiddenField('formId', $formId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
