<?

class WebsiteOptionUtils extends WebsiteOptionDB {

  var $websiteUtils;
  var $moduleUtils;

  function WebsiteOptionUtils() {
    $this->WebsiteOptionDB();
  }

  // Get the options
  function getOptions() {
    global $gWebsiteOptions;

    return($gWebsiteOptions);
  }

  // Get the options not part of the current website package
  function getExtraOptions() {
    $extraOptions = array();

    if ($website = $this->websiteUtils->selectBySystemName($this->websiteUtils->getSetupWebsiteName())) {
      $modules = $this->websiteUtils->getPackageModules($website->getPackage());

      $optionNames = $this->getOptionNames();
      foreach ($optionNames as $optionName) {
        eval("\$optionConstant = $optionName;");
        $moduleName = $this->getOptionModule($optionConstant);

        if (!in_array($moduleName, $modules)) {
          array_push($extraOptions, $optionName);
        }
      }
    }

    return($extraOptions);
  }

  // Get all the option names
  // They are the hard coded constant names
  function getOptionNames() {
    $names = array();

    foreach ($this->getOptions() as $option) {
      list($name, $module, $values) = $option;

      array_push($names, $name);
    }

    return($names);
  }

  // Get the option name
  function getOptionName($optionConstant) {
    $options = $this->getOptions();

    list($name, $module, $values) = $options[$optionConstant];

    return($name);
  }

  // Get the option module
  function getOptionModule($optionConstant) {
    $options = $this->getOptions();

    list($name, $moduleName, $values) = $options[$optionConstant];

    return($moduleName);
  }

  // Get the option from a module
  function getModuleOption($moduleName) {
    foreach ($this->getOptions() as $optionConstant => $option) {
      list($name, $module, $values) = $option;

      if ($module == $moduleName) {
        return($optionConstant);
      }
    }
  }

  // Get the option name from a module
  function getModuleOptionName($moduleName) {
    foreach ($this->getOptions() as $optionConstant => $option) {
      list($name, $module, $values) = $option;

      if ($module == $moduleName) {
        return($name);
      }
    }
  }

  // Get the key of an option
  function getOptionKey($optionConstant, $websiteId) {
    $options = $this->getOptionValues($optionConstant);
    $key = $this->getOptionId($optionConstant, $websiteId);

    return($key);
  }

  // Get the value of an option
  function getOptionValue($optionConstant, $websiteId) {
    $options = $this->getOptionValues($optionConstant);
    $optionId = $this->getOptionId($optionConstant, $websiteId);

    if (isset($options[$optionId])) {
      return($options[$optionId]);
    }
  }

  // Get the possible value of an option
  function getOptionId($optionConstant, $websiteId) {
    $optionName = $this->getOptionName($optionConstant);
    if ($websiteOption = $this->selectByNameAndWebsiteId($optionName, $websiteId)) {
      $value = $websiteOption->getValue();
      return($value);
    }
  }

  // Get the option possible values
  function getOptionValues($optionConstant) {
    $options = $this->getOptions();

    list($name, $module, $values) = $options[$optionConstant];

    if (is_array($values)) {
      array_unshift($values, '');
    }

    return($values);
  }

  // Get the option description
  function getOptionDescription($optionConstant) {
    $moduleName = $this->getOptionModule($optionConstant);
    $moduleConstant = $this->moduleUtils->getModuleConstant($moduleName);
    $description = $this->moduleUtils->getModuleDescription($moduleConstant);

    return($description);
  }

  // Remove a website option
  function removeOption($id) {
    $this->delete($id);
  }

}

?>
