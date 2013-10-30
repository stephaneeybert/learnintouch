<?PHP

require_once("website.php");

$mlText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$pattern = LibEnv::getEnvHttpPOST("pattern");

if (!$pattern) {
  $pattern = LibEnv::getEnvHttpGET("pattern");
}

$pattern = LibString::cleanString($pattern);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[4]</div>";

$str .= $commonUtils->renderGoogleSearch($pattern, $mlText[0], $mlText[1] . ' ' . $gSetupWebsiteUrl, $gSetupWebsiteUrl);

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
