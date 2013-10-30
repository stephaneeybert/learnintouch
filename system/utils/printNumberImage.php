<?PHP

require_once("website.php");

$code = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
$securityCodeFontSize = LibEnv::getEnvHttpGET("securityCodeFontSize");

if ($code) {
  LibImage::printTTFNumberImage($code, $securityCodeFontSize);
}

?>
