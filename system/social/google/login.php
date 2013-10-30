<?PHP

require_once("website.php");

$code = LibEnv::getEnvHttpGET("code");

if ($code) {
  $googleUtils->processLoginRequest($code);
} else {
  $url = $googleUtils->getAuthenticateCodeUrl();

  $str = LibHtml::urlRedirect($url);
  printContent($str);
  return;
}

?>
