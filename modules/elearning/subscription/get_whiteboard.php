<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$whiteboard = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $whiteboard = $elearningSubscription->getWhiteboard();
  $whiteboard = LibString::jsonEscapeLinebreak($whiteboard);
  $whiteboard = LibString::escapeDoubleQuotes($whiteboard);
}

$responseText = <<<HEREDOC
{
  "whiteboard" : "$whiteboard"
}
HEREDOC;

print($responseText);

?>
