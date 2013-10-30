<?php

class User {

  var $id;
  var $firstname;
  var $lastname;
  var $organisation;
  var $email;
  var $homePhone;
  var $workPhone;
  var $fax;
  var $mobilePhone;
  var $password;
  var $passwordSalt;
  var $readablePassword;
  var $unconfirmedEmail;
  var $validUntil;
  var $lastLogin;
  var $profile;
  var $image;
  var $imported;
  var $mailSubscribe;
  var $smsSubscribe;
  var $creationDateTime;
  var $addressId;

  function User($id = '') {
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

  function getOrganisation() {
    return($this->organisation);
  }

  function getEmail() {
    return($this->email);
  }

  function getHomePhone() {
    return($this->homePhone);
  }

  function getWorkPhone() {
    return($this->workPhone);
  }

  function getFax() {
    return($this->fax);
  }

  function getMobilePhone() {
    return($this->mobilePhone);
  }

  function getPassword() {
    return($this->password);
  }

  function getPasswordSalt() {
    return($this->passwordSalt);
  }

  function getReadablePassword() {
    return($this->readablePassword);
  }

  function getUnconfirmedEmail() {
    return($this->unconfirmedEmail);
  }

  function getValidUntil() {
    return($this->validUntil);
  }

  function getLastLogin() {
    return($this->lastLogin);
  }

  function getProfile() {
    return($this->profile);
  }

  function getImage() {
    return($this->image);
  }

  function getImported() {
    return($this->imported);
  }

  function getMailSubscribe() {
    return($this->mailSubscribe);
  }

  function getSmsSubscribe() {
    return($this->smsSubscribe);
  }

  function getCreationDateTime() {
    return($this->creationDateTime);
  }

  function getAddressId() {
    return($this->addressId);
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

  function setOrganisation($organisation) {
    $this->organisation = $organisation;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setHomePhone($homePhone) {
    $this->homePhone = $homePhone;
  }

  function setWorkPhone($workPhone) {
    $this->workPhone = $workPhone;
  }

  function setFax($fax) {
    $this->fax = $fax;
  }

  function setMobilePhone($mobilePhone) {
    $this->mobilePhone = $mobilePhone;
  }

  function setPassword($password) {
    $this->password = $password;
  }

  function setPasswordSalt($passwordSalt) {
    $this->passwordSalt = $passwordSalt;
  }

  function setReadablePassword($readablePassword) {
    $this->readablePassword = $readablePassword;
  }

  function setUnconfirmedEmail($unconfirmedEmail) {
    $this->unconfirmedEmail = $unconfirmedEmail;
  }

  function setValidUntil($validUntil) {
    $this->validUntil = $validUntil;
  }

  function setLastLogin($lastLogin) {
    $this->lastLogin = $lastLogin;
  }

  function setProfile($profile) {
    $this->profile = $profile;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setImported($imported) {
    $this->imported = $imported;
  }

  function setMailSubscribe($mailSubscribe) {
    $this->mailSubscribe = $mailSubscribe;
  }

  function setSmsSubscribe($smsSubscribe) {
    $this->smsSubscribe = $smsSubscribe;
  }

  function setCreationDateTime($creationDateTime) {
    $this->creationDateTime = $creationDateTime;
  }

  function setAddressId($addressId) {
    $this->addressId = $addressId;
  }

}

?>
