<?

// Check for a url specified model
// A model can be passed as an argument in the url to display a specific model
// This is to use a model other than the default model and is not required
// It is used in the navigation elements
if (!isset($templateModelId)) {
  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");
}

// Prevent sql injection attacks
// The id is always numeric
$templateModelId = (int) $templateModelId;

// Check for a url specified model name
// A model name can be passed as an argument in the url to display a specific model
// This is only for manual use when typing a url in the browser url
// It is not used in any other part of the system
// It is used directly in the browser url field like
// demo.thalasoft.net/engine/system/template/display.php?templateModelName=myModelName
if (!$templateModelId) {
  $templateModelName = LibEnv::getEnvHttpGET("templateModelName");
  if ($templateModelName) {
    if ($templateModel = $templateModelUtils->selectByName($templateModelName)) {
      $templateModelId = $templateModel->getId();
    }
  }
}

// If no model is specified then check for a current model
if (!$templateModelId) {
  $templateModelId = $templateUtils->getCurrentModel();
  // Otherwise get the default model
  // Also get the default model if no specific page is requested and the requested url is the domain name
  if ($templateUtils->isBaseUrl($REQUEST_URI) || !$templateModelId) {
    $templateUtils->detectUserAgent();  
    // Check if the client is a phone
    $gIsPhoneClient = $templateUtils->isPhoneClient();
    if ($gIsPhoneClient) {
      $templateModelId = $templateUtils->getPhoneDefault();
    } else {
      // Reset the client as not being a phone
      $templateUtils->unsetPhoneClient();

      // Otherwise get the computer default model
      $templateModelId = $templateUtils->getComputerDefault();
    }
  }
} else {
  // Set the passed model as the current model
  $templateUtils->setCurrentModel($templateModelId);
}

// Render the template
$str = $templateUtils->renderContent($templateModelId);

// Output into a buffer
print($str);

?>
