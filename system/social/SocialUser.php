<?php

class SocialUser {

  var $id;
  var $facebookUserId;
  var $linkedinUserId;
  var $googleUserId;
  var $userId;

  function SocialUser($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getFacebookUserId() {
    return($this->facebookUserId);
  }

  function getLinkedinUserId() {
    return($this->linkedinUserId);
  }

  function getGoogleUserId() {
    return($this->googleUserId);
  }

  function getUserId() {
    return($this->userId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setFacebookUserId($facebookUserId) {
    $this->facebookUserId = $facebookUserId;
  }

  function setLinkedinUserId($linkedinUserId) {
    $this->linkedinUserId = $linkedinUserId;
  }

  function setGoogleUserId($googleUserId) {
    $this->googleUserId = $googleUserId;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

}

?>
