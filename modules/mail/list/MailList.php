<?php

class MailList {

  var $id;
  var $name;
  var $description;
  var $autoSubscribe;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function getAutoSubscribe() {
    return($this->autoSubscribe);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setAutoSubscribe($autoSubscribe) {
    $this->autoSubscribe = $autoSubscribe;
  }

}

?>
