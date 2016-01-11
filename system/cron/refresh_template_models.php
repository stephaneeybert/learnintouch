<?PHP

$specific = '';
if ($argc == 2) {
  $specific = $argv[1];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

require_once("cli.php");

$languages = $languageUtils->getActiveLanguageCodes();

$templateModels = $templateModelUtils->selectAll();
foreach ($templateModels as $templateModel) {
  $templateModelId = $templateModel->getId();
  if ($templateModel = $templateModelUtils->selectById($templateModelId)) {

    // Cache the html file for the model
    $templateUtils->cacheModelFile($templateModelId);

    $templateModelUtils->cacheCssFile($templateModelId);

    foreach ($languages as $language) {
      $languageUtils->setCurrentLanguageCode($language, false);

      $str = $templateModelUtils->renderWebsiteModel($templateModelId);
      if ($str) {
        $filename = $templateUtils->getModelFilename($templateModelId);
        if ($filename) {
          LibFile::writeString($filename, $str);
        }
      }
    }
  }
}

?>
