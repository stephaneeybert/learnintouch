<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
$whiteboard = LibEnv::getEnvHttpPOST("whiteboard");

if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $elearningSubscription->setWhiteboard($whiteboard);
  $elearningSubscriptionUtils->update($elearningSubscription);

  $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
}

?>
