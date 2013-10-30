<?

class AdminOptionUtils extends AdminOptionDB {

  var $websiteOptionUtils;
  var $websiteUtils;

  function AdminOptionUtils() {
    $this->AdminOptionDB();
  }

  // Check if an option is granted to an administrator
  function isAdminOption($optionConstant, $adminId) {
    $isOption = false;

    $optionName = $this->websiteOptionUtils->getOptionName($optionConstant);
    if ($adminOption = $this->selectByNameAndAdmin($optionName, $adminId)) {
      $isOption = true;
    }

    return($isOption);
  }

  // Delete the options of an admin
  function deleteAdminOptions($adminId) {
    if ($adminOptions = $this->selectByAdmin($adminId)) {
      foreach ($adminOptions as $adminOption) {
        $adminOptionId = $adminOption->getId();
        $this->delete($adminOptionId);
      }
    }
  }

  // Get the possible value of an option
  function getOptionValue($optionConstant, $adminId) {
    $optionName = $this->websiteOptionUtils->getOptionName($optionConstant);
    if ($adminOption = $this->selectByNameAndAdmin($optionName, $adminId)) {
      $value = $adminOption->getValue();
      return($value);
    }
  }

  // Remove admin option permissions for the options no longer granted to the web site
  // (if the package of the web site has now less or other options)
  function removeNonGrantedOptions($adminId) {
    if ($adminOptions = $this->selectByAdmin($adminId)) {
      foreach ($adminOptions as $adminOption) {
        $option = $adminOption->getName();
        if (!$this->websiteUtils->isCurrentWebsiteOption($option)) {
          $this->delete($adminOption->getId());
        }
      }
    }
  }

  function grantOption($adminId, $optionName) {
    if ($this->websiteUtils->isCurrentWebsiteOption($optionName)) {
      $website = $this->websiteUtils->selectBySystemName($this->websiteUtils->getSetupWebsiteName());
      $websiteId = $website->getId();
      eval("\$optionConstant = $optionName;");
      $value = $this->websiteOptionUtils->getOptionKey($optionConstant, $websiteId);

      if (!$adminOption = $this->selectByNameAndAdmin($optionName, $adminId)) {
        $adminOption = new AdminOption();
        $adminOption->setName($optionName);
        $adminOption->setAdmin($adminId);
        $adminOption->setValue($value);
        $this->insert($adminOption);
      }
    }
  }

}

?>
