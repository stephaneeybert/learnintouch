<?PHP

require_once("website.php");

// Prevent any possible previous header from being sent before the following image header that must be the first
ob_end_clean();

$code = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
$securityCodeFontSize = LibEnv::getEnvHttpGET("securityCodeFontSize");

if ($code) {
  LibImage::printTTFNumberImage($code, $securityCodeFontSize);
}

?>
