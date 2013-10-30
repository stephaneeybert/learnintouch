<?php

class MailAddress {

  var $id;
  var $firstname;
  var $lastname;
  var $email;
  var $comment;
  var $country;
  var $subscribe;
  var $imported;
  var $creationDateTime;

  function MailAddress($id = '') {
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

  function getComment() {
    return($this->comment);
  }

  function getCountry() {
    return($this->country);
  }

  function getSubscribe() {
    return($this->subscribe);
  }

  function getImported() {
    return($this->imported);
  }

  function getCreationDateTime() {
    return($this->creationDateTime);
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

  function setComment($comment) {
    $this->comment = $comment;
  }

  function setCountry($country) {
    $this->country = $country;
  }

  function setSubscribe($subscribe) {
    $this->subscribe = $subscribe;
  }

  function setImported($imported) {
    $this->imported = $imported;
  }

  function setCreationDateTime($creationDateTime) {
    $this->creationDateTime = $creationDateTime;
  }

}

?>
