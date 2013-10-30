<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $userId = LibEnv::getEnvHttpPOST("userId");

  // Check that the user is specified
  if (!$user = $userUtils->selectById($userId)) {
    array_push($warnings, $mlText[3]);
  }

  // Check that the user is not already assigned to another teacher
  if ($elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    $elearningTeacher = new ElearningTeacher();
    $elearningTeacher->setUserId($userId);
    $elearningTeacherUtils->insert($elearningTeacher);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/admin.php");
    printContent($str);
    return;

  }

} else {

  $userId = '';

}

$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/teacher/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('userId', $userId);
$strCommand = "<a href='$gUserUrl/add.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[5]'></a>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='userName' value='$userName' /> $strCommand", "n"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
