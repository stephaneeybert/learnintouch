<?php

class LocationState {

  var $id;
  var $code;
  var $name;
  var $upperName;
  var $region;
  var $country;

  function LocationState($id = '') {
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

  function getUpperName() {
    return($this->upperName);
  }

  function getRegion() {
    return($this->region);
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

  function setUpperName($upperName) {
    $this->upperName = $upperName;
  }

  function setRegion($region) {
    $this->region = $region;
  }

  function setCountry($country) {
    $this->country = $country;
  }

}

?>
