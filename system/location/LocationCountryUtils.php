<?

class LocationCountryUtils extends LocationCountryDB {

  function __construct() {
    parent::__construct();
  }

  // Get the name of a country
  function getCountryName($code) {
    $name = '';

    if ($locationCountry = $this->selectByCode($code)) {
      $name = $locationCountry->getName();
    }

    return($name);
  }

  // Get the code of a country
  function getCountryCode($name) {
    $code = '';

    if ($locationCountry = $this->selectByName($name)) {
      $code = $locationCountry->getCode();
    }

    return($code);
  }

}

?>
