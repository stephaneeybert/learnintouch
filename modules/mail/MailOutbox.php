<?php

class MailOutbox {

  var $id;
  var $firstname;
  var $lastname;
  var $email;
  var $password;
  var $sent;
  var $errorMessage;
  var $metaNames;

  function MailOutbox($id = '') {
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

  function getEmail() {
    return($this->email);
  }

  function getPassword() {
    return($this->password);
  }

  function getSent() {
    return($this->sent);
  }

  function getErrorMessage() {
    return($this->errorMessage);
  }

  function getMetaNames() {
    return($this->metaNames);
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

  function setEmail($email) {
    $this->email = $email;
  }

  function setPassword($password) {
    $this->password = $password;
  }

  function setSent($sent) {
    $this->sent = $sent;
  }

  function setErrorMessage($errorMessage) {
    $this->errorMessage = $errorMessage;
  }

  function setMetaNames($metaNames) {
    $this->metaNames = $metaNames;
  }

}

?>
