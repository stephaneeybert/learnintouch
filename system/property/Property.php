<?php

class Property {

  var $id;
  var $name;
  var $value;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getValue() {
    return($this->value);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setValue($value) {
    $this->value = $value;
  }

}

?>
