<?PHP

require_once("website.php");

$oauth_token = LibEnv::getEnvHttpGET("oauth_token");
$oauth_verifier = LibEnv::getEnvHttpGET("oauth_verifier");

if ($oauth_token && $oauth_verifier) {
  $twitterUtils->convertRequestToAccessToken($oauth_token, $oauth_verifier);
} else {
  $authenticateToken = $twitterUtils->getAuthenticateToken();

  if ($authenticateToken) {
    $url = "https://api.twitter.com/oauth/authenticate?oauth_token=$authenticateToken";

    $str = LibHtml::urlRedirect($url);
    printContent($str);
    return;
  } else {
    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }
}

?>
