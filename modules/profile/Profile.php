<?php

// The web site profile is stored as a series of name/value pairs.

class Profile {

  var $id;
  var $name;
  var $value;

  function Profile($id = '') {
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
