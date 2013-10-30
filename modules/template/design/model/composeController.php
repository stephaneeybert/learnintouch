<?PHP

$elementType = LibEnv::getEnvHttpPOST("elementType");
$templateElementId = LibEnv::getEnvHttpPOST("templateElementId");
$templateContainerId = LibEnv::getEnvHttpPOST("templateContainerId");

if (!$templateModelId) {
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");
} if (!$templateModelId) {
  $templateModelId = $templateUtils->getCurrentModel();
} else {
  $templateUtils->setCurrentModel($templateModelId);
}

// Swap a container with the previous one
$swapPreviousContainer = LibEnv::getEnvHttpPOST("swapPreviousContainer");
if ($swapPreviousContainer) {
  $templateContainerUtils->swapWithPrevious($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Swap a container with the next one
$swapNextContainer = LibEnv::getEnvHttpPOST("swapNextContainer");
if ($swapNextContainer) {
  $templateContainerUtils->swapWithNext($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Swap a row of containers with the previous row
$swapRowContainersWithPrevious = LibEnv::getEnvHttpPOST("swapRowContainersWithPrevious");
if ($swapRowContainersWithPrevious) {
  $swapRow = LibEnv::getEnvHttpPOST("swapRow");
  $templateModelUtils->swapRowContainersWithPrevious($templateModelId, $swapRow);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Swap a row of containers with the next row
$swapRowContainersWithNext = LibEnv::getEnvHttpPOST("swapRowContainersWithNext");
if ($swapRowContainersWithNext) {
  $swapRow = LibEnv::getEnvHttpPOST("swapRow");
  $templateModelUtils->swapRowContainersWithNext($templateModelId, $swapRow);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Move a container to the previous row
$moveToPreviousRow = LibEnv::getEnvHttpPOST("moveToPreviousRow");
if ($moveToPreviousRow) {
  $templateModelUtils->moveContainerIntoPreviousRow($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Move a container to the next row
$moveToNextRow = LibEnv::getEnvHttpPOST("moveToNextRow");
if ($moveToNextRow) {
  $templateModelUtils->moveContainerIntoNextRow($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Delete a container
$deleteContainer = LibEnv::getEnvHttpPOST("deleteContainer");
if ($deleteContainer) {
  $templateContainerUtils->deleteTemplateContainer($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Add a row of containers
$addRow = LibEnv::getEnvHttpPOST("addRow");
if ($addRow) {
  $templateModelUtils->addRow($templateModelId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Add a container
$addContainer = LibEnv::getEnvHttpPOST("addContainer");
if ($addContainer) {
  $templateContainerUtils->addTemplateContainer($templateContainerId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Swap an element with the previous one
$swapPreviousElement = LibEnv::getEnvHttpPOST("swapPreviousElement");
if ($swapPreviousElement) {
  $templateElementUtils->swapWithPrevious($templateElementId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Swap an element with the next one
$swapNextElement = LibEnv::getEnvHttpPOST("swapNextElement");
if ($swapNextElement) {
  $templateElementUtils->swapWithNext($templateElementId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Add an element
$addElement = LibEnv::getEnvHttpPOST("addElement");
if ($addElement && trim($elementType)) {
  $templateElementUtils->addElement($templateContainerId, $elementType);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Hide or show an element
$showHideElement = LibEnv::getEnvHttpPOST("showHideElement");
if ($showHideElement) {
  $templateElementUtils->showHideElement($templateElementId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

// Delete an element
$deleteElement = LibEnv::getEnvHttpPOST("deleteElement");
if ($deleteElement) {
  $templateElementUtils->deleteElement($templateElementId);
  $templateUtils->setRefreshCache();

  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  exit;
}

?>
