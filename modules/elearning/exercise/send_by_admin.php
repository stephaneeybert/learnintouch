<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $email = LibEnv::getEnvHttpPOST("email");

  $email = LibString::cleanString($email);

  // A recipient is required
  if (!$elearningClassId && !$elearningSubscriptionId && !$email) {
    array_push($warnings, $mlText[40]);
  }

  $emailRecipients = array();

  if ($elearningClassId) {
    if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        $userId = $elearningSubscription->getUserId();
        if ($user = $userUtils->selectById($userId)) {
          $firstname = $user->getFirstname();
          $lastname = $user->getLastname();
          $email = $user->getEmail();
          array_push($emailRecipients, array($email, $firstname, $lastname));
        }
      }
    }
    if (count($emailRecipients) == 0) {
      array_push($warnings, $mlText[14]);
    }
  } else if ($elearningSubscriptionId) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $elearningSubscriptionId = $elearningSubscription->getId();
      $userId = $elearningSubscription->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        array_push($emailRecipients, array($email, $firstname, $lastname));
      }
    }
  } else {
    // The email must have an email format
    if ($email && !LibEmail::validate($email)) {
      array_push($warnings, $mlText[38]);
    }

    // The email is case insensitive
    $email = strtolower($email);

    array_push($emailRecipients, array($email, '', ''));
  }

  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }

  // Check that there are some email addresses
  if (count($emailRecipients) == 0) {
    array_push($warnings, $mlText[40]);
  }

  if (count($warnings) == 0) {

    $websiteName = $profileUtils->getProfileValue("website.name");
    $websiteEmail = $profileUtils->getProfileValue("website.email");

    $subject = $mlText[8] . ' ' . $websiteName;

    foreach ($emailRecipients as $emailRecipient) {
      list($email, $firstname, $lastname) = $emailRecipient;

      if ($firstname) {
        $strHello = $mlText[15] . ' ' . $firstname . ',';
      } else {
        $strHello = $mlText[15] . ',';
      }

      $body = $strHello . ' '
      . "<br><br>" . $mlText[6]
      . "<br><br>" . $mlText[9] . ' '
      . "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId'>"
      . $name
      . "</a>"
      . "<br><br><br>" . $mlText[7]
      . "<br><br>" . $websiteName;

      if ($email) {
        LibEmail::sendMail($email, $email, $subject, $body, $websiteEmail, $websiteName);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$strPreview = $popupUtils->getDialogPopup($mlText[10], "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[16], "cb"));
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[13], $mlText[12], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', '');
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='className' value='' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 200);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/subscription/suggest.php", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', '');
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='participantName' value='' />");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='email' value='' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->addCell($strPreview, ''));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
