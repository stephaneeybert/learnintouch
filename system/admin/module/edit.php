<?PHP

require_once("website.php");

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$moduleNames = $websiteUtils->getCurrentWebsiteModules();

$optionNames = $websiteUtils->getCurrentWebsiteOptions();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $adminId = LibEnv::getEnvHttpPOST("adminId");

  foreach ($moduleNames as $moduleName) {
    $adminCheckedModule = LibEnv::getEnvHttpPOST("module_$moduleName");

    $adminCheckedModule = LibString::cleanString($adminCheckedModule);

    eval("\$moduleConstant = $moduleName;");

    // Check that the module was not already granted
    if ($adminCheckedModule == 1) {
      if (!$adminModuleUtils->isAdminModule($moduleConstant, $adminId)) {
        $adminModule = new AdminModule();
        $adminModule->setModule($moduleName);
        $adminModule->setAdmin($adminId);
        $adminModuleUtils->insert($adminModule);
      }
    } else {
      if ($adminModule = $adminModuleUtils->selectByModuleAndAdmin($moduleName, $adminId)) {
        $adminModuleUtils->delete($adminModule->getId());
      }
    }

  }

  foreach ($optionNames as $optionName) {
    eval("\$optionConstant = $optionName;");

    $optionValues = $websiteOptionUtils->getOptionValues($optionConstant);

    if ($optionValues) {
      $value = LibEnv::getEnvHttpPOST("value_$optionName");

      $value = LibString::cleanString($value);

      if ($value) {
        if ($adminOption = $adminOptionUtils->selectByNameAndAdmin($optionName, $adminId)) {
          $adminOption->setValue($value);
          $adminOptionUtils->update($adminOption);
        } else {
          $adminOption = new AdminOption();
          $adminOption->setName($optionName);
          $adminOption->setAdmin($adminId);
          $adminOption->setValue($value);
          $adminOptionUtils->insert($adminOption);
        }
      } else {
        if ($adminOption = $adminOptionUtils->selectByNameAndAdmin($optionName, $adminId)) {
          $adminOptionUtils->delete($adminOption->getId());
        }
      }
    } else {
      $checkedOption = LibEnv::getEnvHttpPOST("option_$optionName");

      $checkedOption = LibString::cleanString($checkedOption);

      if ($checkedOption == 1) {
        if (!$adminOptionUtils->isAdminOption($optionConstant, $adminId)) {
          $adminOption = new AdminOption();
          $adminOption->setName($optionName);
          $adminOption->setAdmin($adminId);
          $adminOptionUtils->insert($adminOption);
        }
      } else {
        if ($adminOption = $adminOptionUtils->selectByNameAndAdmin($optionName, $adminId)) {
          $adminOptionUtils->delete($adminOption->getId());
        }
      }
    }

  }

  $str = LibHtml::urlRedirect("$gAdminUrl/list.php");
  printContent($str);
  return;

} else {

  $adminId = LibEnv::getEnvHttpGET("adminId");

  if ($admin = $adminUtils->selectById($adminId)) {
    $firstname = $admin->getFirstname();
    $lastname = $admin->getLastname();
  }

  $panelUtils->setHeader($mlText[0], "$gAdminUrl/list.php");
  $help = $popupUtils->getHelpPopup($mlText[8], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);

  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "$firstname $lastname", '');
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), '', '');
  $panelUtils->addLine();


  foreach ($moduleNames as $moduleName) {
    $moduleConstant = $moduleUtils->getModuleConstant($moduleName);

    $moduleDescription = $moduleUtils->getModuleDescription($moduleConstant);

    if ($adminModuleUtils->isAdminModule($moduleConstant, $adminId)) {
      $checkedModule = "CHECKED";
    } else {
      $checkedModule = '';
    }

    $panelUtils->addLine($panelUtils->addCell($moduleDescription, "nbr"), "<input type='checkbox' name='module_$moduleName' $checkedModule value='1'>", '');
  }

  foreach ($optionNames as $optionName) {
    eval("\$optionConstant = $optionName;");

    $optionDescription = $websiteOptionUtils->getOptionDescription($optionConstant);
    $optionValues = $websiteOptionUtils->getOptionValues($optionConstant);

    if ($optionValues) {
      if ($adminOptionUtils->isAdminOption($optionConstant, $adminId)) {
        $value = $adminOptionUtils->getOptionValue($optionConstant, $adminId);
      } else {
        $value = '';
      }
      $selectOption = LibHtml::getSelectList("value_$optionName", $optionValues, $value);
      $panelUtils->addLine($panelUtils->addCell($optionDescription, "nbr"), $selectOption, '');
    } else {
      if ($adminOptionUtils->isAdminOption($optionConstant, $adminId)) {
        $checkedOption = "CHECKED";
      } else {
        $checkedOption = '';
      }
      $panelUtils->addLine($panelUtils->addCell($optionDescription, "nbr"), "<input type='checkbox' name='option_$optionName' $checkedOption value='1'>", '');
    }
  }

  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk(), '');
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('adminId', $adminId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
