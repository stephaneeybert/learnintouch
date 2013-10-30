<?PHP

require_once("website.php");


// The administrator may access this page without being logged in if a unique token is used
// This allows an administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  $email = LibEnv::getEnvHttpGET("email");
  if ($user = $userUtils->selectByEmail($email)) {
    $userUtils->openUserSession($email);
  }
} else {
  $elearningExerciseUtils->checkUserLogin();
}

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

$userId = $userUtils->getLoggedUserId();
$email = $userUtils->getUserEmail();

if ($elearningResultUtils->belongsToUser($userId) || $elearningResultUtils->belongsToEmail($email)) {
  $str = $elearningResultUtils->renderResult($elearningResultId);

  $gTemplate->setPageContent($str);

  $elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
  if ($elearningTemplateModelId > 0) {
    $templateModelId = $elearningTemplateModelId;
  }

  require_once($gTemplatePath . "render.php");
}

?>
