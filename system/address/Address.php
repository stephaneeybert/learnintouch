<?php

class Address {

  var $id;
  var $address1;
  var $address2;
  var $zipCode;
  var $city;
  var $state;
  var $country;
  var $postalBox;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getAddress1() {
    return($this->address1);
  }

  function getAddress2() {
    return($this->address2);
  }

  function getZipCode() {
    return($this->zipCode);
  }

  function getCity() {
    return($this->city);
  }

  function getState() {
    return($this->state);
  }

  function getCountry() {
    return($this->country);
  }

  function getPostalBox() {
    return($this->postalBox);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setAddress1($address1) {
    $this->address1 = $address1;
  }

  function setAddress2($address2) {
    $this->address2 = $address2;
  }

  function setZipCode($zipCode) {
    $this->zipCode = $zipCode;
  }

  function setCity($city) {
    $this->city = $city;
  }

  function setState($state) {
    $this->state = $state;
  }

  function setCountry($country) {
    $this->country = $country;
  }

  function setPostalBox($postalBox) {
    $this->postalBox = $postalBox;
  }

}

?>
