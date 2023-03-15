<?php

class WebsiteSubscription {

  var $id;
  var $openingDate;
  var $fee;
  var $duration;
  var $autoRenewal;
  var $terminationDate;
  var $websiteId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getOpeningDate() {
    return($this->openingDate);
  }

  function getFee() {
    return($this->fee);
  }

  function getDuration() {
    return($this->duration);
  }

  function getAutoRenewal() {
    return($this->autoRenewal);
  }

  function getTerminationDate() {
    return($this->terminationDate);
  }

  function getWebsiteId() {
    return($this->websiteId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setOpeningDate($openingDate) {
    $this->openingDate = $openingDate;
  }

  function setFee($fee) {
    $this->fee = $fee;
  }

  function setDuration($duration) {
    $this->duration = $duration;
  }

  function setAutoRenewal($autoRenewal) {
    $this->autoRenewal = $autoRenewal;
  }

  function setTerminationDate($terminationDate) {
    $this->terminationDate = $terminationDate;
  }

  function setWebsiteId($websiteId) {
    $this->websiteId = $websiteId;
  }

}

?>
