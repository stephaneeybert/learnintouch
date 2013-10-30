<?PHP


$templateElementLanguageId = LibEnv::getEnvHttpPOST("templateElementLanguageId");
$objectId = LibEnv::getEnvHttpPOST("objectId");


if ($templateElementLanguage = $templateElementLanguageUtils->selectById($templateElementLanguageId)) {
  $templateElementLanguage->setObjectId($objectId);
  $templateElementLanguageUtils->update($templateElementLanguage);
}

$str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
printContent($str);
return;

?>
