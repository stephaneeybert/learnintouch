<?

class LocationStateUtils extends LocationStateDB {

  var $locationCountryUtils;

  function LocationStateUtils() {
    $this->LocationStateDB();
  }

  // Get the list of state names
  function getStateNames($country) {
    $nameList = array();

    if ($country) {
      $countryCode = $this->locationCountryUtils->getCountryCode($country);
      if ($locationStates = $this->selectByCountry($countryCode)) {
        foreach ($locationStates as $locationState) {
          $name = $locationState->getName();
          array_push($nameList, $name);
        }
      }
    }

    return($nameList);
  }

  // Get the name of a state
  function getStateName($code) {
    $name = '';

    if ($locationState = $this->selectByCode($code)) {
      $name = $locationState->getName();
    }

    return($name);
  }

  // Get the code of a state
  function getStateCode($name) {
    $code = '';

    if ($locationState = $this->selectByName($name)) {
      $code = $locationState->getCode();
    }

    return($code);
  }

}

?>
