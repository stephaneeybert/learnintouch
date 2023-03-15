<?php

class People {

  var $id;
  var $firstname;
  var $lastname;
  var $email;
  var $workPhone;
  var $mobilePhone;
  var $profile;
  var $image;
  var $categoryId;
  var $listOrder;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getFirstname() {
    return($this->firstname);
  }

  function getLastname() {
    return($this->lastname);
  }

  function getEmail() {
    return($this->email) ;
  }

  function getWorkPhone() {
    return($this->workPhone);
  }

  function getMobilePhone() {
    return($this->mobilePhone);
  }

  function getProfile() {
    return($this->profile);
  }

  function getImage() {
    return($this->image);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setFirstname($firstname) {
    $this->firstname = $firstname;
  }

  function setLastname($lastname) {
    $this->lastname = $lastname;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setWorkPhone($workPhone) {
    $this->workPhone = $workPhone;
  }

  function setMobilePhone($mobilePhone) {
    $this->mobilePhone = $mobilePhone;
  }

  function setProfile($profile) {
    $this->profile = $profile;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
