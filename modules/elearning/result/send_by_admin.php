<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
  $email = LibEnv::getEnvHttpPOST("email");

  $email = LibString::cleanString($email);

  // The email is required
  if (!$email) {
    array_push($warnings, $mlText[40]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[38]);
  }

  if (count($warnings) == 0) {

    $elearningResultUtils->sendResult($elearningResultId, $email);

    $str = LibHtml::urlRedirect("$gElearningUrl/result/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");
  if (!$elearningResultId) {
    $elearningResultId = LibEnv::getEnvHttpPOST("elearningResultId");
  }

}

$elearningExerciseId = '';
$exerciseDate = '';
$exerciseTime = '';
$firstname = '';
$lastname = '';
$email = '';
if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $elearningExerciseId = $elearningResult->getElearningExerciseId();
  $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
  $exerciseTime = $clockUtils->dateTimeToSystemTime($elearningResult->getExerciseDate());
  $firstname = $elearningResult->getFirstname();
  $lastname = $elearningResult->getLastname();
  $email = $elearningResult->getEmail();
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
}

if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
  }
}

// Get the exercise details
$name = '';
$description = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/result/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$name - $description");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "$firstname $lastname $email");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "$exerciseDate $exerciseTime");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningResultId', $elearningResultId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
