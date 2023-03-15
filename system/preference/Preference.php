<?php

class Preference {

  var $id;
  var $name;
  var $value;
  var $type;

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

  function getType() {
    return($this->type);
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

  function setType($type) {
    $this->type = $type;
  }

}

?>
