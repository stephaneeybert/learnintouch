<?

class UniqueTokenUtils extends UniqueTokenDB {

  var $clockUtils;

  function UniqueTokenUtils() {
    $this->UniqueTokenDB();
  }

  // Create and store a token
  function create($name, $days = '') {
    $value = '';

    if ($name) {
      $value = LibUtils::generateUniqueId(UNIQUE_TOKEN_LENGTH);
      $creation = $this->clockUtils->getSystemDateTime();
      $expiration = '';
      if ($days) {
        $expiration = $this->clockUtils->incrementDays($creation, $days);
      }

      $uniqueToken = new UniqueToken();
      $uniqueToken->setName($name);
      $uniqueToken->setValue($value);
      $uniqueToken->setCreationDateTime($creation);
      $uniqueToken->setExpirationDateTime($expiration);
      $this->insert($uniqueToken);
    }

    return($value);
  }

  // Check if a token is valid
  function isValid($name, $value) {
    $valid = false;

    if ($uniqueToken = $this->selectByNameAndValue($name, $value)) {
      $uniqueTokenId = $uniqueToken->getId();
      if ($this->hasExpired($uniqueTokenId)) {
        $this->deleteToken($name, $value);
      } else {
        $valid = true;
      }
    }

    return($valid);
  }

  // Check if a token has expired
  function hasExpired($uniqueTokenId) {
    $expired = false;

    if ($uniqueToken = $this->selectById($uniqueTokenId)) {
      $expiration = $uniqueToken->getExpirationDateTime();
      $now = $this->clockUtils->getSystemDateTime();
      if ($expiration && $expiration < $now) {
        $expired = true;
      }
    }

    return($expired);
  }

  // Delete a token
  function deleteToken($name, $value) {
    if ($uniqueToken = $this->selectByNameAndValue($name, $value)) {
      $uniqueTokenId = $uniqueToken->getId();

      $this->delete($uniqueTokenId);
    }
  }

}

?>
