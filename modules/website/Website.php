<?php

class Website {

  var $id;
  var $name;
  var $systemName;
  var $dbName;
  var $domainName;
  var $firstname;
  var $lastname;
  var $email;
  var $diskSpace;
  var $package;

  function Website($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getSystemName() {
    return($this->systemName);
  }

  function getDbName() {
    return($this->dbName);
  }

  function getDomainName() {
    return($this->domainName);
  }

  function getFirstname() {
    return($this->firstname);
  }

  function getLastname() {
    return($this->lastname);
  }

  function getEmail() {
    return($this->email);
  }

  function getDiskSpace() {
    return($this->diskSpace);
  }

  function getPackage() {
    return($this->package);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setSystemName($systemName) {
    $this->systemName = $systemName;
  }

  function setDbName($dbName) {
    $this->dbName = $dbName;
  }

  function setDomainName($domainName) {
    $this->domainName = $domainName;
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

  function setDiskSpace($diskSpace) {
    $this->diskSpace = $diskSpace;
  }

  function setPackage($package) {
    $this->package = $package;
  }

}

?>
