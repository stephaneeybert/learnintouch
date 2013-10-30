<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$templateElementId = LibEnv::getEnvHttpGET("templateElementId");
$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
$tagID = LibEnv::getEnvHttpGET("tagID");

$filename = $templateUtils->getModelCssPath($templateModelId);
$lines = LibFile::readIntoLines($filename);
$lineNumbers = LibUtils::searchArraySubstring($tagID, $lines);
foreach ($lineNumbers as $lineNumber) {
  $lines[$lineNumber] = '';
}
LibFile::writeArray($filename, $lines);

if ($templateTags = $templateTagUtils->selectByTemplateElementIdAndTagID($templateElementId, $tagID)) {
  foreach ($templateTags as $templateTag) {
    $templateTagId = $templateTag->getId();
    $templateTagUtils->deleteTemplateTag($templateTagId);
  }
}

$str = LibHtml::urlRedirect("$gTemplateUrl/design/element/editProperty.php?templateElementId=$templateElementId");
printMessage($str);
return;

?>
