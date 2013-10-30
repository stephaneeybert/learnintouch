<?php

// to a list of mobile phone numbers or to a particular mobile phone.

class SmsHistory {

  var $id;
  var $smsId;
  var $smsListId;
  var $mobilePhone;
  var $adminId;
  var $sendDate;
  var $nbRecipients;

  function SmsHistory($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getSmsId() {
    return($this->smsId);
  }

  function getSmsListId() {
    return($this->smsListId);
  }

  function getMobilePhone() {
    return($this->mobilePhone);
  }

  function getAdminId() {
    return($this->adminId);
  }

  function getSendDate() {
    return($this->sendDate);
  }

  function getNbRecipients() {
    return($this->nbRecipients);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setSmsId($smsId) {
    $this->smsId = $smsId;
  }

  function setSmsListId($smsListId) {
    $this->smsListId = $smsListId;
  }

  function setMobilePhone($mobilePhone) {
    $this->mobilePhone = $mobilePhone;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

  function setSendDate($sendDate) {
    $this->sendDate = $sendDate;
  }

  function setNbRecipients($nbRecipients) {
    $this->nbRecipients = $nbRecipients;
  }

}

?>
