<?php

class Guestbook {

  var $id;
  var $body;
  var $releaseDate;
  var $userId;
  var $email;
  var $firstname;
  var $lastname;

  function Guestbook($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getBody() {
    return($this->body);
    }

  function getReleaseDate() {
    return($this->releaseDate);
    }

  function getUserId() {
    return($this->userId);
    }

  function getEmail() {
    return($this->email);
    }

  function getFirstname() {
    return($this->firstname);
    }

  function getLastname() {
    return($this->lastname);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setBody($body) {
    $this->body = $body;
    }

  function setReleaseDate($releaseDate) {
    $this->releaseDate = $releaseDate;
    }

  function setUserId($userId) {
    $this->userId = $userId;
    }

  function setEmail($email) {
    $this->email = $email;
    }

  function setFirstname($firstname) {
    $this->firstname = $firstname;
    }

  function setLastname($lastname) {
    $this->lastname = $lastname;
    }

  }

?>
