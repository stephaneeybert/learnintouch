<?php

class LocationZipCode {

  var $id;
  var $code;
  var $name;
  var $country;

  function LocationZipCode($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getCode() {
    return($this->code);
  }

  function getName() {
    return($this->name);
  }

  function getCountry() {
    return($this->country);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setCode($code) {
    $this->code = $code;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setCountry($country) {
    $this->country = $country;
  }

}

?>
