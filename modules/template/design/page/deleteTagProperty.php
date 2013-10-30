<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
$systemPage = LibEnv::getEnvHttpGET("systemPage");
$templatePageId = LibEnv::getEnvHttpGET("templatePageId");
$tagID = LibEnv::getEnvHttpGET("tagID");

$filename = $templateUtils->getModelCssPath($templateModelId);
$lines = LibFile::readIntoLines($filename);
$lineNumbers = LibUtils::searchArraySubstring($tagID, $lines);
foreach ($lineNumbers as $lineNumber) {
  $lines[$lineNumber] = '';
}
LibFile::writeArray($filename, $lines);

if ($templatePageTags = $templatePageTagUtils->selectByTemplatePageIdAndTagID($templatePageId, $tagID)) {
  foreach ($templatePageTags as $templatePageTag) {
    $templatePageTagId = $templatePageTag->getId();
    if ($templatePageTags = $templatePageTagUtils->selectByTemplatePageId($templatePageId)) {
      $templatePageTagUtils->deleteTemplatePageTag($templatePageTagId);
    }
  }
}

$str = LibHtml::urlRedirect("$gTemplateUrl/design/page/editProperty.php?systemPage=$systemPage&templateModelId=$templateModelId");
printMessage($str);
return;

?>
