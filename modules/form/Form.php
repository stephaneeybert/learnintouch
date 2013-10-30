<?php

class Form {

  var $id;
  var $name;
  var $description;
  var $title;
  var $image;
  var $email;
  var $instructions;
  var $acknowledge;
  var $webpageId;
  var $mailSubject;
  var $mailMessage;

  function Form($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function getTitle() {
    return($this->title);
  }

  function getImage() {
    return($this->image);
  }

  function getEmail() {
    return($this->email);
  }

  function getInstructions() {
    return($this->instructions);
  }

  function getAcknowledge() {
    return($this->acknowledge);
  }

  function getWebpageId() {
    return($this->webpageId);
  }

  function getMailSubject() {
    return($this->mailSubject);
  }

  function getMailMessage() {
    return($this->mailMessage);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setTitle($title) {
    $this->title = $title;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setInstructions($instructions) {
    $this->instructions = $instructions;
  }

  function setAcknowledge($acknowledge) {
    $this->acknowledge = $acknowledge;
  }

  function setWebpageId($webpageId) {
    $this->webpageId = $webpageId;
  }

  function setMailSubject($mailSubject) {
    $this->mailSubject = $mailSubject;
  }

  function setMailMessage($mailMessage) {
    $this->mailMessage = $mailMessage;
  }

}

?>
