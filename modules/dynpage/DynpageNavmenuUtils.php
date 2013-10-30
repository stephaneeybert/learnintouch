<?

class DynpageNavmenuUtils extends DynpageNavmenuDB {

  var $dynpageUtils;

  function DynpageNavmenuUtils() {
    $this->DynpageNavmenuDB();
  }

  // Add a dynpage menu
  function add() {
    $dynpageNavmenu = new DynpageNavmenu();
    $this->insert($dynpageNavmenu);
    $dynpageNavmenuId = $this->getLastInsertId();

    return($dynpageNavmenuId);
  }

  // Duplicate a dynpage menu
  function duplicate($dynpageNavmenuId) {
    if ($dynpageNavmenu = $this->selectById($dynpageNavmenuId)) {
      $this->insert($dynpageNavmenu);
      $duplicatedDynpageNavmenuId = $this->getLastInsertId();

      return($duplicatedDynpageNavmenuId);
    }
  }

  // Render a navigation menu
  function render($elementType, $dynpageNavmenuId) {
    $str = '';

    if ($dynpageNavmenu = $this->selectById($dynpageNavmenuId)) {
      $parentId = $dynpageNavmenu->getParentId();

      if ($elementType == 'DYNPAGE_MENU') {
        $str = $this->dynpageUtils->renderMenu($parentId);
      } else if ($elementType == 'DYNPAGE_ACCORDION_MENU') {
        $str = $this->dynpageUtils->renderAccordionMenu($parentId);
      } else if ($elementType == 'DYNPAGE_TREE_MENU') {
        $str = $this->dynpageUtils->renderDirectoryTree($parentId);
      }
    }

    return($str);
  }

}

?>
