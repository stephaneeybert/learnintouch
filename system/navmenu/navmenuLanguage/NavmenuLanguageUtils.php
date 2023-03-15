<?

class NavmenuLanguageUtils extends NavmenuLanguageDB {

  var $navmenuItemUtils;

  function __construct() {
    parent::__construct();
  }

  // Delete a language
  function deleteLanguage($id) {
    // Delete all the items of the language
    if ($navmenuLanguage = $this->selectById($id)) {
      $navmenuItemId = $navmenuLanguage->getNavmenuItemId();

      $this->delete($id);

      $this->navmenuItemUtils->delete($navmenuItemId);
    }
  }

}

?>
