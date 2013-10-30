<?php

class SmsOutbox {

  var $id;
  var $firstname;
  var $lastname;
  var $mobilePhone;
  var $email;
  var $password;
  var $sent;

  function SmsOutbox($id = '') {
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

  function getEmail() {
    return($this->email);
    }

  function getPassword() {
    return($this->password);
    }

  function getSent() {
    return($this->sent);
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

  function setEmail($email) {
    $this->email = $email;
    }

  function setPassword($password) {
    $this->password = $password;
    }

  function setSent($sent) {
    $this->sent = $sent;
    }

  }

?>
