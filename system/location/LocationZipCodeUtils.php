<?

class LocationZipCodeUtils extends LocationZipCodeDB {

  var $locationCountryUtils;
  var $locationStateUtils;

  function __construct() {
    parent::__construct();
  }

  // Get the list of city names
  function getCityNames($country, $state) {
    $nameList = array();

    if ($country && $state) {
      $countryCode = $this->locationCountryUtils->getCountryCode($country);
      if (!is_numeric($state)) {
        $state = $this->locationStateUtils->getStateCode($state);
      }
      if (is_numeric($state)) {
        $stateCode = substr($state, 0, 2);
      } else {
        $stateCode = '';
      }
      if ($locationZipCodes = $this->selectByCountryAndState($countryCode,
        $stateCode)) {
          foreach ($locationZipCodes as $locationZipCode) {
            $name = $locationZipCode->getName();
            array_push($nameList, $name);
          }
        }
    }

    return($nameList);
  }

}

?>
