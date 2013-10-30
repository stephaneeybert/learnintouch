<?php

class Contact {

  var $id;
  var $firstname;
  var $lastname;
  var $email;
  var $organisation;
  var $telephone;
  var $subject;
  var $message;
  var $contactDate;
  var $garbage;
  var $status;
  var $contactRefererId;

  function Contact($id = '') {
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

  function getOrganisation() {
    return($this->organisation);
  }

  function getTelephone() {
    return($this->telephone);
  }

  function getSubject() {
    return($this->subject);
  }

  function getMessage() {
    return($this->message);
  }

  function getContactDate() {
    return($this->contactDate);
  }

  function getGarbage() {
    return($this->garbage);
  }

  function getStatus() {
    return($this->status);
  }

  function getContactRefererId() {
    return($this->contactRefererId);
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

  function setOrganisation($organisation) {
    $this->organisation = $organisation;
  }

  function setTelephone($telephone) {
    $this->telephone = $telephone;
  }

  function setSubject($subject) {
    $this->subject = $subject;
  }

  function setMessage($message) {
    $this->message = $message;
  }

  function setContactDate($contactDate) {
    $this->contactDate = $contactDate;
  }

  function setGarbage($garbage) {
    $this->garbage = $garbage;
  }

  function setStatus($status) {
    $this->status = $status;
  }

  function setContactRefererId($contactRefererId) {
    $this->contactRefererId = $contactRefererId;
  }

}

?>
