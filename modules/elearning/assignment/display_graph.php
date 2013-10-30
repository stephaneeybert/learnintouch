<?PHP

require_once("website.php");

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

if (!$elearningSubscriptionId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$userUtils->checkValidUserLogin();

if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $userUtils->getLoggedUserId();
  $elearningSubscriptionUtils->checkUserSubscription($userId, $elearningSubscription);

  $str = $elearningAssignmentUtils->renderGraph($elearningSubscriptionId);

  print($templateUtils->renderPopup($str));
}

?>
