<?php

// It has a name and a value.
// Its value is unique among all the values.
// There can be several tokens with the same name.
// A token has a creation timestamp and an expiration timestamp.
// A token whose expiration timestamp has passed is not valid any longer.

class UniqueToken {

  var $id;
  var $name;
  var $value;
  var $creationDateTime;
  var $expirationDateTime;

  function UniqueToken($id = '') {
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

  function getCreationDateTime() {
    return($this->creationDateTime);
  }

  function getExpirationDateTime() {
    return($this->expirationDateTime);
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

  function setCreationDateTime($creationDateTime) {
    $this->creationDateTime = $creationDateTime;
  }

  function setExpirationDateTime($expirationDateTime) {
    $this->expirationDateTime = $expirationDateTime;
  }

}

?>
