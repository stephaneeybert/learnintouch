<?php

class SmsListNumber {

  var $id;
  var $smsListId;
  var $smsNumberId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getSmsListId() {
    return($this->smsListId);
  }

  function getSmsNumberId() {
    return($this->smsNumberId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setSmsListId($smsListId) {
    $this->smsListId = $smsListId;
  }

  function setSmsNumberId($smsNumberId) {
    $this->smsNumberId = $smsNumberId;
  }

}

?>
