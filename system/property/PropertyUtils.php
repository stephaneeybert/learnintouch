<?

class PropertyUtils extends PropertyDB {

  function __construct() {
    parent::__construct();
  }

  // Store a property
  function store($name, $value) {
    if ($property = $this->selectByName($name)) {
      $property->setValue($value);
      $this->update($property);
    } else {
      $property = new Property();
      $property->setName($name);
      $property->setValue($value);
      $this->insert($property);
    }
  }

  // Retrieve the value of a property
  function retrieve($name) {
    $value = '';

    if ($property = $this->selectByName($name)) {
      $value = $property->getValue();
    }

    return($value);
  }

}

?>
