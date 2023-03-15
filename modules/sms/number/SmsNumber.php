<?php

class SmsNumber {

  var $id;
  var $firstname;
  var $lastname;
  var $mobilePhone;
  var $subscribe;
  var $import;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getFirstname() {
    return($this->firstname);
  }

  function getLastname() {
    return($this->lastname);
  }

  function getMobilePhone() {
    return($this->mobilePhone);
  }

  function getSubscribe() {
    return($this->subscribe);
  }

  function getImported() {
    return($this->import);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setFirstname($firstname) {
    $this->firstname = $firstname;
  }

  function setLastname($lastname) {
    $this->lastname = $lastname;
  }

  function setMobilePhone($mobilePhone) {
    $this->mobilePhone = $mobilePhone;
  }

  function setSubscribe($subscribe) {
    $this->subscribe = $subscribe;
  }

  function setImported($import) {
    $this->import = $import;
  }

}

?>
