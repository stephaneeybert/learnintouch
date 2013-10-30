<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templatePropertySetId = LibEnv::getEnvHttpPOST("templatePropertySetId");
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");
  $tagID = LibEnv::getEnvHttpPOST("tagID");

  $templatePropertySetUtils->loadPropertyTypes();
  foreach ($templatePropertySetUtils->propertyTypes as $name => $data) {
    $value = LibEnv::getEnvHttpPOST("value_$name");
    $value = LibString::cleanString($value);

    // A margin is required to align a div
    // See the hack to have a div align on the left, center, right
    if ($name == 'MARGIN' && !$value) {
      $value = 0;
    }

    // Check if a value is set
    // The zero is indeed a value!
    if (strlen($value) > 0) {
      // Add a hash before a color code if none
      if (strstr("value_$name", 'COLOR') && substr($value, 0, 1) != '#') {
        $value = '#' . $value;
      }

      if ($templateProperty = $templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, $name)) {
        $templateProperty->setValue($value);
        $templatePropertyUtils->update($templateProperty);
      } else {
        $templateProperty = new TemplateProperty();
        $templateProperty->setName($name);
        $templateProperty->setValue($value);
        $templateProperty->setTemplatePropertySetId($templatePropertySetId);
        $templatePropertyUtils->insert($templateProperty);
      }
    } else {
      if ($templateProperty = $templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, $name)) {
        $propertyId = $templateProperty->getId();
        $templatePropertyUtils->delete($propertyId);
      }
    }
  }

  // Update the cached properties file for the model
  $strProperties = $templatePropertySetUtils->renderHtmlProperties($templatePropertySetId);
  $newLine = '.' . $tagID . ' { ' . $strProperties . ' }';
  $strTextProperties = $templatePropertySetUtils->renderHtmlTextProperties($templatePropertySetId);
  if (trim($strTextProperties)) {
    $newLine .= ' .' . $tagID . ' a { ' . $strTextProperties . ' }';
  }
  $newLine .= ' ' . $templatePropertySetUtils->renderHtmlLinkProperties(".$tagID", $templatePropertySetId);
  $newLine .= ' /* TPS_ID_' . $templatePropertySetId . ' */' . "\n";

  $filename = $templateUtils->getModelCssPath($templateModelId);
  $lines = LibFile::readIntoLines($filename);
  $lineId = 'TPS_ID_' . $templatePropertySetId . ' ';
  $lineNumbers = LibUtils::searchArraySubstring($lineId, $lines);
  // There should theoretically be only one line
  if (count($lineNumbers) > 0) {
    foreach ($lineNumbers as $lineNumber) {
      $lines[$lineNumber] = $newLine;
    }
  } else {
    $lines[count($lines)] = $newLine;
  }
  LibFile::writeArray($filename, $lines);

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  exit;
}

?>
