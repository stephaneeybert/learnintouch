<?php

class Mail {

  var $id;
  var $subject;
  var $body;
  var $description;
  var $textFormat;
  var $attachments;
  var $creationDate;
  var $sendDate;
  var $locked;
  var $adminId;
  var $categoryId;

  function Mail($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getSubject() {
    return($this->subject);
  }

  function getBody() {
    return($this->body);
  }

  function getDescription() {
    return($this->description);
  }

  function getTextFormat() {
    return($this->textFormat);
  }

  function getAttachments() {
    return($this->attachments);
  }

  function getCreationDate() {
    return($this->creationDate);
  }

  function getSendDate() {
    return($this->sendDate);
  }

  function getLocked() {
    return($this->locked);
  }

  function getAdminId() {
    return($this->adminId);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setSubject($subject) {
    $this->subject = $subject;
  }

  function setBody($body) {
    $this->body = $body;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setTextFormat($textFormat) {
    $this->textFormat = $textFormat;
  }

  function setAttachments($attachments) {
    $this->attachments = $attachments;
  }

  function setCreationDate($creationDate) {
    $this->creationDate = $creationDate;
  }

  function setSendDate($sendDate) {
    $this->sendDate = $sendDate;
  }

  function setLocked($locked) {
    $this->locked = $locked;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

}

?>
