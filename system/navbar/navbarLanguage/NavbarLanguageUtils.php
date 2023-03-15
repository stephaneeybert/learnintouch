<?

class NavbarLanguageUtils extends NavbarLanguageDB {

  var $navbarItemUtils;

  function __construct() {
    parent::__construct();
  }

  // Delete a language
  function deleteLanguage($id) {
    // Delete all the items of the language
    if ($navbarItems = $this->navbarItemUtils->selectByNavbarLanguageId($id)) {
      foreach ($navbarItems as $navbarItem) {
        $navbarItemId = $navbarItem->getId();
        $this->navbarItemUtils->deleteItem($navbarItemId);
      }
    }

    $this->delete($id);
  }

}

?>
