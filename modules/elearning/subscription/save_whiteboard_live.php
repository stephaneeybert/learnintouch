<?PHP

require_once("website.php");

LibHtml::preventCaching();

$whiteboard = LibEnv::getEnvHttpPOST("whiteboard");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

if ($elearningSubscriptionId) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningSubscription->setWhiteboard($whiteboard);
    $elearningSubscriptionUtils->update($elearningSubscription);

    $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
  }
}

?>
