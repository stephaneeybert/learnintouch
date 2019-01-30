<?

class WebsiteUtils extends WebsiteDB {

  var $mlText;

  // The packages are used to grant usage permissions
  // These array values are used in a database table
  // Do not modify these array values
  var $packageNames;

  // The modules for each package
  // The order of packages in the array matters!
  // The modules of each package are added to the previous ones
  var $packageModules;

  // The options
  // The options are added on top of a package for a web site
  var $options;

  var $languageUtils;
  var $websiteSubscriptionUtils;
  var $sqlToolsUtils;
  var $moduleUtils;
  var $websiteOptionUtils;
  var $websiteAddressUtils;

  function WebsiteUtils() {
    $this->WebsiteDB();

    $this->init();
  }

  function init() {
    $this->packageModules = array(
      'PACKAGE_BASIC' =>
      array(
        'MODULE_PROFILE',
        'MODULE_LANGUAGE',
        'MODULE_TEMPLATE',
        'MODULE_CLOCK',
        'MODULE_DYNPAGE',
        'MODULE_FORM',
        'MODULE_CLIENT',
        'MODULE_LINK',
        'MODULE_CONTACT',
        'MODULE_PHOTO',
        'MODULE_BACKUP',
      ),
      'PACKAGE_STANDARD' =>
      array(
        'MODULE_NEWS',
        'MODULE_PEOPLE',
        'MODULE_FLASH',
        'MODULE_DOCUMENT',
      ),
      'PACKAGE_ADVANCED' =>
      array(
        'MODULE_USER',
        'MODULE_SECURED_PAGES',
        'MODULE_GUESTBOOK',
        'MODULE_STATISTICS',
        'MODULE_MAIL',
        'MODULE_SMS',
      ),
    );
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Get the package names
  function getPackageNames() {
    $this->loadLanguageTexts();

    $this->packageNames = array(
      'PACKAGE_BASIC' => $this->mlText[0],
      'PACKAGE_STANDARD' => $this->mlText[1],
      'PACKAGE_ADVANCED' => $this->mlText[2],
    );

    return($this->packageNames);
  }

  // Get the package name
  function getPackageName($packageConstant) {
    $packageNames = $this->getPackageNames();

    $name = $packageNames[$packageConstant];

    return($name);
  }

  // Get the name of the current web site
  function getSetupWebsiteName() {
    global $gSetupWebsiteName;

    return($gSetupWebsiteName);
  }

  // Get the domain name of the current web site
  function getSetupWebsiteDomainName() {
    global $gSetupWebsiteUrl;

    $domainName = '';

    // Remove the protocol
    $str = str_replace('http://', '', $gSetupWebsiteUrl);
    $str = str_replace('https://', '', $gSetupWebsiteUrl);

    // Remove sub domain names
    $bits = explode('.', $str);
    if (count($bits) > 1) {
      $domainName = $bits[count($bits) - 2] . '.' . $bits[count($bits) - 1];
    }

    return($domainName);
  }

  // Get the modules of a package
  function getPackageModules($package) {
    $listPackages = array();

    if ($package) {
      // Add up the modules of the package
      foreach($this->packageModules as $key => $value) {
        $listPackages = array_merge($listPackages, $this->packageModules[$key]);
        if ($key == $package) {
          break;
        }
      }
    }

    // Remove any double entries (but there shouldn't be any)
    $listPackages = array_unique($listPackages);

    return($listPackages);
  }

  // Check the size of the website
  function checkWebsiteSize() {
    if ($website = $this->selectBySystemName($this->getSetupWebsiteName())) {
      $name = $website->getName();
      $dbName = $website->getDbName();
      $domainName = $website->getDomainName();
      $diskSpace = $website->getDiskSpace();

      $strDomainName = "<a href='$domainName'>$domainName</a>";

      $dbNames = $this->sqlToolsUtils->getDatabaseNames();
      if (!in_array($dbName, $dbNames)) {
        reportError("The name $dbName is not a database.", '', 'system/website/usage.php');
      }

      // Get the disk usage
      $diskUsage = $this->getTotalDiskUsage($name, $dbName);
      $percentDiskUsage = ($diskUsage * 100) / $diskSpace . '%';
      if ($percentDiskUsage > 100) {
        emailStaff("Disk usage: $percentDiskUsage - $domainName is using $diskUsage for an allocated disk space of $diskSpace");
      }
    }
  }

  // Get the option of a module if any
  function getModuleOption($moduleConstant) {
    $moduleName = $this->moduleUtils->getModuleName($moduleConstant);

    $optionConstant = $this->websiteOptionUtils->getModuleOption($moduleName);

    return($optionConstant);
  }

  // Check if a module belongs to a website
  // Normally this function is passed a module name, but if a module constant is passed
  // then get the module name from the module constant
  function isCurrentWebsiteModule($moduleName) {
    $isModule = false;

    // Get the package modules for the current website
    $websiteModules = $this->getCurrentWebsiteModules();

    // Get the module name is a module constant was passed to the function
    if (is_numeric($moduleName)) {
      $moduleName = $this->moduleUtils->getModuleName($moduleName);
    }

    if (in_array($moduleName, $websiteModules)) {
      $isModule = true;
    }

    return($isModule);
  }

  // Check if an option belongs to a website
  function isWebsiteOption($option, $websiteId) {
    // Get the options for the website
    $websiteOptions = $this->getOptions($websiteId);

    if (in_array($option, $websiteOptions)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check that an option belongs to the current website
  function checkCurrentWebsiteOption($option) {
    global $gAdminUrl;

    $isOption = $this->isCurrentWebsiteOption($option);

    if (!$isOption) {
      $str = LibHtml::urlDisplayRedirect("$gAdminUrl/login.php");
      printContent($str);
      return;
    }
  }

  // Check if an option belongs to the current website
  function isCurrentWebsiteOption($option) {
    $isOption = false;

    if ($website = $this->selectBySystemName($this->getSetupWebsiteName())) {
      $websiteId = $website->getId();

      // Get the options for the website
      $websiteOptions = $this->getOptions($websiteId);

      if (in_array($option, $websiteOptions)) {
        $isOption = true;
      }
    }

    return($isOption);
  }

  // Get the modules of the current web site
  function getCurrentWebsiteModules() {
    $modules = array();

    if ($website = $this->selectBySystemName($this->getSetupWebsiteName())) {
      $modules = $this->getPackageModules($website->getPackage());
    }

    return($modules);
  }

  // Get the options of a web site
  function getOptions($websiteId) {
    $options = array();

    if ($websiteOptions = $this->websiteOptionUtils->selectByWebsiteId($websiteId)) {
      foreach ($websiteOptions as $websiteOption) {
        $name = $websiteOption->getName();
        array_push($options, $name);
      }
    }

    return($options);
  }

  // Get the options of the current web site
  function getCurrentWebsiteOptions() {
    $options = array();

    if ($website = $this->selectBySystemName($this->getSetupWebsiteName())) {
      $websiteId = $website->getId();
      $options = $this->getOptions($websiteId);
    }

    return($options);
  }

  // Get the names of all the accounts
  function getAccountNames() {
    $names = array();

    if ($websites = $this->selectAll()) {
      foreach ($websites as $website) {
        array_push($names, $website->getName());
      }
    }

    return($names);
  }

  // Check if the current website is terminated
  function isTerminated() {
    $isTerminated = false;

    if ($website = $this->selectBySystemName($this->getSetupWebsiteName())) {
      $websiteId = $website->getId();
      // Check if the last subscription has passed
      if ($this->websiteSubscriptionUtils->hasExpired($websiteId)) {
        $isTerminated = true;
      }
    }

    return($isTerminated);
  }

  // Get the disk space used by a web site
  function getDiskUsage($path) {
    // Get the size of the directory content, in mb
    $diskUsage = LibFile::getDirectorySize($path);

    return($diskUsage);
  }

  // Get the database size for a web site
  function getDatabaseSize($dbName) {
    $dbSize = 0;

    $dbSize = $this->sqlToolsUtils->getDatabaseSize();

    return($dbSize);
  }

  // Get the total disk usage
  function getTotalDiskUsage($name, $dbName) {
    global $gWebsitesPath;

    // Get the disk usage
    $path = $gWebsitesPath . $name . "/account/data";
    $diskUsage = $this->getDiskUsage($path);

    // Get the database size
    $dbSize = $this->getDatabaseSize($dbName);

    // The total size
    $totalSize = $diskUsage + $dbSize;

    return($totalSize);
  }

  // Delete the directories and files for an account
  function deleteAccount($websiteId) {
    global $gAccountPath;

    // Delete the address if any
    if ($address = $this->websiteAddressUtils->selectByWebsite($websiteId)) {
      $addressId = $address->getId();
      $this->websiteAddressUtils->delete($addressId);
    }

    // Delete the subscription if any
    if ($websiteSubscriptions = $this->websiteSubscriptionUtils->selectByWebsiteId($websiteId)) {
      foreach ($websiteSubscriptions as $websiteSubscription) {
        $websiteSubscriptionId = $websiteSubscription->getId();
        $this->websiteSubscriptionUtils->delete($websiteSubscriptionId);
      }
    }

    $this->delete($websiteId);

    return(true);
  }

}

?>
