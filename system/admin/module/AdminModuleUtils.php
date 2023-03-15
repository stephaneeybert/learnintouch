<?

class AdminModuleUtils extends AdminModuleDB {

  var $mlText;

  var $languageUtils;
  var $moduleUtils;
  var $adminUtils;
  var $websiteUtils;
  var $adminOptionUtils;
  var $websiteOptionUtils;

  function __construct() {
    parent::__construct();
  }

  function init() {
    $languageCode = $this->languageUtils->getCurrentAdminLanguageCode();
    $this->mlText = $this->languageUtils->getText(__FILE__, $languageCode);
  }

  // Check if a module is granted to an administrator
  function isAdminModule($moduleConstant, $adminId) {
    $moduleName = $this->moduleUtils->getModuleName($moduleConstant);
    if ($adminModule = $this->selectByModuleAndAdmin($moduleName, $adminId)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if a module is granted to the currently logged in administrator
  function moduleGrantedToAdmin($moduleConstant) {
    $isGranted = false;

    // Check if the admin is logged in and get the login name
    $login = $this->adminUtils->getSessionLogin();

    // Get the admin id
    if ($admin = $this->adminUtils->selectByLogin($login)) {
      $adminId = $admin->getId();

      $isGranted = $this->isAdminModule($moduleConstant, $adminId);

      // Check if the module is granted as an option
      if (!$isGranted) {
        $optionConstant = $this->websiteUtils->getModuleOption($moduleConstant);
        if ($optionConstant) {
          $isGranted = $this->adminOptionUtils->isAdminOption($optionConstant, $adminId);
        }
      }
    } else if ($this->adminUtils->isStaffLogin($login)) {
      $isGranted = true;
    }

    return($isGranted);
  }

  // Get the modules granted to the currently logged in administrator
  function getLoggedAdminModules() {
    $adminModules = array();

    $moduleNames = $this->websiteUtils->getCurrentWebsiteModules();
    foreach ($moduleNames as $moduleName) {
      eval("\$moduleConstant = $moduleName;");
      if ($this->moduleGrantedToAdmin($moduleConstant)) {
        array_push($adminModules, $moduleName);
      }
    }

    $optionNames = $this->websiteOptionUtils->getExtraOptions();
    foreach ($optionNames as $optionName) {
      eval("\$optionConstant = $optionName;");
      $moduleName = $this->websiteOptionUtils->getOptionModule($optionConstant);
      eval("\$moduleConstant = $moduleName;");
      if ($this->moduleGrantedToAdmin($moduleConstant)) {
        array_push($adminModules, $moduleName);
      }
    }

    return($adminModules);
  }

  // Check that the administrator can use the module
  function checkAdminModule($moduleConstant) {
    global $gAdminUrl;

    $this->adminUtils->checkAdminLogin();

    if ($this->moduleGrantedToAdmin($moduleConstant)) {
      return(true);
    }

    $str = $this->mlText[0];
    $str .= LibHtml::urlDisplayRedirect("$gAdminUrl/menu.php", 5);
    printMessage($str);
    exit;
  }

  // Remove admin module permissions for the modules and options
  // no longer granted to the web site
  // (if the package of the web site has now less or other modules)
  function removeNonGranted($adminId) {
    $this->removeNonGrantedModules($adminId);

    $this->adminOptionUtils->removeNonGrantedOptions($adminId);
  }

  // Delete the modules of an admin
  function deleteAdminModules($adminId) {
    if ($adminModules = $this->selectByAdmin($adminId)) {
      foreach ($adminModules as $adminModule) {
        $adminModuleId = $adminModule->getId();
        $this->delete($adminModuleId);
      }
    }
  }

  function removeNonGrantedModules($adminId) {
    if ($adminModules = $this->selectByAdmin($adminId)) {
      foreach ($adminModules as $adminModule) {
        $module = $adminModule->getModule();
        if (!$this->websiteUtils->isCurrentWebsiteModule($module) && !$this->websiteUtils->isCurrentWebsiteOption($module)) {
          $this->delete($adminModule->getId());
        }
      }
    }
  }

  // Grant a module
  function grantModule($adminId, $moduleName) {
    eval("\$moduleConstant = $moduleName;");
    if (!$this->isAdminModule($moduleConstant, $adminId)) {
      $adminModule = new AdminModule();
      $adminModule->setModule($moduleName);
      $adminModule->setAdmin($adminId);
      $this->insert($adminModule);
    }
  }

  // Grant all the modules for an administrator
  function grantAllModules($adminId) {
    $moduleNames = $this->websiteUtils->getCurrentWebsiteModules();
    foreach ($moduleNames as $moduleName) {
      eval("\$moduleConstant = $moduleName;");
      if (!$this->isAdminModule($moduleConstant, $adminId)) {
        $adminModule = new AdminModule();
        $adminModule->setModule($moduleName);
        $adminModule->setAdmin($adminId);
        $this->insert($adminModule);
      }
    }
  }

}

?>
