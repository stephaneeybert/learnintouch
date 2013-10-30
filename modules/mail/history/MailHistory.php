<?php

// to a list of email addresses or to a particular email address.

class MailHistory {

  var $id;
  var $subject;
  var $body;
  var $description;
  var $attachments;
  var $mailListId;
  var $email;
  var $adminId;
  var $sendDate;

  function MailHistory($id = '') {
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

  function getAttachments() {
    return($this->attachments);
  }

  function getMailListId() {
    return($this->mailListId);
  }

  function getEmail() {
    return($this->email);
  }

  function getAdminId() {
    return($this->adminId);
  }

  function getSendDate() {
    return($this->sendDate);
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

  function setAttachments($attachments) {
    $this->attachments = $attachments;
  }

  function setMailListId($mailListId) {
    $this->mailListId = $mailListId;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

  function setSendDate($sendDate) {
    $this->sendDate = $sendDate;
  }

}

?>
