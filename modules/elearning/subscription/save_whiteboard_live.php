<?PHP

require_once("website.php");

LibHtml::preventCaching();

$whiteboard = LibEnv::getEnvHttpPOST("whiteboard");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
$elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");

if ($elearningSubscriptionId) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningSubscription->setWhiteboard($whiteboard);
    $elearningSubscriptionUtils->update($elearningSubscription);

    $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
  }
}

if ($elearningClassId) {
  if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId)) {
    foreach ($elearningSubscriptions as $elearningSubscription) {
      $elearningSubscription->setWhiteboard($whiteboard);
      $elearningSubscriptionUtils->update($elearningSubscription);

      $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
    }
  }
}

?>
