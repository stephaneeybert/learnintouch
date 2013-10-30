<?php

class MailListUser {

  var $id;
  var $mailListId;
  var $userId;

  function MailListUser($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getMailListId() {
    return($this->mailListId);
    }

  function getUserId() {
    return($this->userId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setMailListId($mailListId) {
    $this->mailListId = $mailListId;
    }

  function setUserId($userId) {
    $this->userId = $userId;
    }

  }

?>
