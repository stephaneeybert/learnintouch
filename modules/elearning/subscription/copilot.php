<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$lastExercisePageId = LibEnv::getEnvHttpGET("lastExercisePageId");

if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  // Set the subscription as being watched live if not yet done
  if (!$elearningSubscription->getWatchLive()) {
    $elearningSubscription->setWatchLive(true);
    $elearningSubscriptionUtils->update($elearningSubscription);
  }
  $userId = $elearningSubscription->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $email = $user->getEmail();
    $userUtils->openUserSession($email);

    $adminId = $adminUtils->getLoggedAdminId();
    error_log("Storing the adminId: $adminId");
    LibSession::putSessionValue(ADMIN_SESSION_ADMIN_ID, $adminId);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId&elearningExercisePageId=$lastExercisePageId");
    printContent($str);
    return;
  }
}

printAdminPage($mlText[0]);

?>
