<?php

class Admin {

  var $id;
  var $firstname;
  var $lastname;
  var $login;
  var $password;
  var $passwordSalt;
  var $superAdmin;
  var $preferenceAdmin;
  var $address;
  var $zipCode;
  var $city;
  var $country;
  var $email;
  var $profile;
  var $postLoginUrl;

  function Admin($id = '') {
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

  function getLogin() {
    return($this->login);
  }

  function getPassword() {
    return($this->password);
  }

  function getPasswordSalt() {
    return($this->passwordSalt);
  }

  function getSuperAdmin() {
    return($this->superAdmin);
  }

  function getPreferenceAdmin() {
    return($this->preferenceAdmin);
  }

  function getAddress() {
    return($this->address);
  }

  function getZipCode() {
    return($this->zipCode);
  }

  function getCity() {
    return($this->city);
  }

  function getCountry() {
    return($this->country);
  }

  function getEmail() {
    return($this->email);
  }

  function getProfile() {
    return($this->profile);
  }

  function getPostLoginUrl() {
    return($this->postLoginUrl);
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

  function setLogin($login) {
    $this->login = $login;
  }

  function setPassword($password) {
    $this->password = $password;
  }

  function setPasswordSalt($passwordSalt) {
    $this->passwordSalt = $passwordSalt;
  }

  function setSuperAdmin($superAdmin) {
    $this->superAdmin = $superAdmin;
  }

  function setPreferenceAdmin($preferenceAdmin) {
    $this->preferenceAdmin = $preferenceAdmin;
  }

  function setAddress($address) {
    $this->address = $address;
  }

  function setZipCode($zipCode) {
    $this->zipCode = $zipCode;
  }

  function setCity($city) {
    $this->city = $city;
  }

  function setCountry($country) {
    $this->country = $country;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setProfile($profile) {
    $this->profile = $profile;
  }

  function setPostLoginUrl($postLoginUrl) {
    $this->postLoginUrl = $postLoginUrl;
  }

}

?>
