<?php

class MailListAddress {

  var $id;
  var $mailListId;
  var $mailAddressId;

  function MailListAddress($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getMailListId() {
    return($this->mailListId);
  }

  function getMailAddressId() {
    return($this->mailAddressId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setMailListId($mailListId) {
    $this->mailListId = $mailListId;
  }

  function setMailAddressId($mailAddressId) {
    $this->mailAddressId = $mailAddressId;
  }

}

?>
