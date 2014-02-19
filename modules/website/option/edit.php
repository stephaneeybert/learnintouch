<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$optionNames = $websiteOptionUtils->getExtraOptions();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $websiteId = LibEnv::getEnvHttpPOST("websiteId");

  foreach ($optionNames as $optionName) {
    // Transform the option name into its constant
    eval("\$optionConstant = $optionName;");

    $optionValues = $websiteOptionUtils->getOptionValues($optionConstant);

    if ($optionValues) {
      $value = LibEnv::getEnvHttpPOST("value_$optionName");

      $value = LibString::cleanString($value);

      // If the option is granted
      if ($value) {
        if ($websiteOption = $websiteOptionUtils->selectByNameAndWebsiteId($optionName, $websiteId)) {
          $websiteOption->setValue($value);
          $websiteOptionUtils->update($websiteOption);
        } else {
          $websiteOption = new WebsiteOption();
          $websiteOption->setName($optionName);
          $websiteOption->setWebsiteId($websiteId);
          $websiteOption->setValue($value);
          $websiteOptionUtils->insert($websiteOption);
        }
      } else {
        // An empty value for a select option means the option is not granted
        if ($websiteOption = $websiteOptionUtils->selectByNameAndWebsiteId($optionName, $websiteId)) {
          $websiteOptionUtils->delete($websiteOption->getId());
        }
      }

    } else {
      $checkedOption = LibEnv::getEnvHttpPOST("option_$optionName");

      $checkedOption = LibString::cleanString($checkedOption);

      // If the option is granted to the web site
      if ($checkedOption == 1) {
        // If the option was not already granted
        if (!$websiteUtils->isWebsiteOption($optionName, $websiteId)) {
          $websiteOption = new WebsiteOption();
          $websiteOption->setName($optionName);
          $websiteOption->setWebsiteId($websiteId);
          $websiteOptionUtils->insert($websiteOption);
        }
      } else {
        // If the option was already granted
        if ($websiteOption = $websiteOptionUtils->selectByNameAndWebsiteId($optionName, $websiteId)) {
          $websiteOptionUtils->removeOption($websiteOption->getId());
        }
      }
    }

  }

  $str = LibHtml::urlRedirect("$gWebsiteUrl/admin.php");
  printContent($str);
  return;

} else {

  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  if ($website = $websiteUtils->selectById($websiteId)) {
    $name = $website->getName();
    $domainName = $website->getDomainName();
  }

  $panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[8], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);

  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "$name $domainName");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), '');
  $panelUtils->addLine();

  foreach ($optionNames as $optionName) {
    // Transform the option name into its constant
    eval("\$optionConstant = $optionName;");

    $optionDescription = $websiteOptionUtils->getOptionDescription($optionConstant);
    $optionValues = $websiteOptionUtils->getOptionValues($optionConstant);

    // If a select option
    if ($optionValues) {
      if ($websiteUtils->isWebsiteOption($optionName, $websiteId)) {
        $value = $websiteOptionUtils->getOptionId($optionConstant, $websiteId);
      } else {
        $value = '';
      }

      $selectOption = LibHtml::getSelectList("value_$optionName", $optionValues, $value);
      $panelUtils->addLine($panelUtils->addCell($optionDescription, "br"), $selectOption);
    } else {
      if ($websiteUtils->isWebsiteOption($optionName, $websiteId)) {
        $checkedOption = "CHECKED";
      } else {
        $checkedOption = '';
      }

      $panelUtils->addLine($panelUtils->addCell($optionDescription, "br"), "<input type='checkbox' name='option_$optionName' $checkedOption value='1'>");
    }
  }

  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('websiteId', $websiteId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
