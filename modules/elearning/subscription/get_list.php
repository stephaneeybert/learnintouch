<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

$elearningSubscriptions = array();

if ($elearningSubscriptionId) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    array_push($elearningSubscriptions, $elearningSubscription);
  }
} else if ($elearningClassId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId);
}

$strSubscriptions = '';
foreach ($elearningSubscriptions as $elearningSubscription) {
  $elearningSubscriptionId = $elearningSubscription->getId();
  $elearningCourseId = $elearningSubscription->getCourseId();

  $userId = $elearningSubscription->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $participantName = $firstname . ' ' . $lastname;

    $firstname = LibString::jsonEscapeLinebreak($firstname);
    $firstname = LibString::escapeDoubleQuotes($firstname);
    $lastname = LibString::jsonEscapeLinebreak($lastname);
    $lastname = LibString::escapeDoubleQuotes($lastname);

    $courseName = '';
    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $courseName = $elearningCourse->getName();
      $courseName = LibString::jsonEscapeLinebreak($courseName);
      $courseName = LibString::escapeDoubleQuotes($courseName);
    }

    $strSubscriptions .= "{elearningSubscriptionId : \"$elearningSubscriptionId\", firstname : \"$firstname\", lastname : \"$lastname\", courseName : \"$courseName\"},";
  }
}

$responseText = <<<HEREDOC
{
  "subscriptions" : [ $strSubscriptions ]
}
HEREDOC;

print($responseText);

?>
