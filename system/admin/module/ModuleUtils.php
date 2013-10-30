<?

class ModuleUtils {

  function ModuleUtils() {
    }

  // Get all the module names
  // They are the hard coded constant names
  function getModules() {
    global $gModules;

    return($gModules);
    }

  // Get the module name
  function getModuleName($moduleConstant) {
    $modules = $this->getModules();

    list($name, $description) = $modules[$moduleConstant];

    return($name);
    }

  // Transform the module name into its constant
  function getModuleConstant($moduleName) {

    eval("\$moduleConstant = $moduleName;");

    return($moduleConstant);
    }

  // Get the module description
  function getModuleDescription($moduleConstant) {
    $modules = $this->getModules();

    list($name, $description) = $modules[$moduleConstant];

    return($description);
    }

  }

?>
