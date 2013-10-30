<?php

class SmsListUser {

  var $id;
  var $smsListId;
  var $userId;

  function SmsListUser($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getSmsListId() {
    return($this->smsListId);
    }

  function getUserId() {
    return($this->userId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setSmsListId($smsListId) {
    $this->smsListId = $smsListId;
    }

  function setUserId($userId) {
    $this->userId = $userId;
    }

  }

?>
