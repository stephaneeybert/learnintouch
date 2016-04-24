<?PHP

require_once("website.php");

LibHtml::preventCaching();

$facebookUserId = LibEnv::getEnvHttpGET("facebookUserId");
$noRedirect = LibEnv::getEnvHttpGET("noRedirect");

$userId = '';
$postUserLoginUrl = '';
$email = '';
$firstname = '';
$lastname = '';

if ($facebookUser = $facebookUtils->selectByFacebookUserId($facebookUserId)) {
  $userId = $facebookUser->getUserId();

  if ($user = $userUtils->selectById($userId)) {
    $email = $user->getEmail();
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();

    if (!$userUtils->noLongerValidUserCannotLogin($email)) {
      $userUtils->openUserSession($email);
      if (!$noRedirect) {
        $postUserLoginUrl = $userUtils->getPostUserLoginUrl();
      }
    }
  }
}

$preferenceUtils->init($userUtils->preferences);
$userAutoRegister = $preferenceUtils->getValue("USER_AUTO_REGISTER");

$responseText = <<<HEREDOC
{
"postUserLoginUrl" : "$postUserLoginUrl",
"userAutoRegister" : "$userAutoRegister",
"noRedirect" : "$noRedirect",
"facebookUserId" : "$facebookUserId",
"userId" : "$userId",
"email" : "$email",
"firstname" : "$firstname",
"lastname" : "$lastname"
}
HEREDOC;

print($responseText);

?>
