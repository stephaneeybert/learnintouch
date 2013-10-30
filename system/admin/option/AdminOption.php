<?php

class AdminOption {

  var $id;
  var $name;
  var $admin;
  var $value;

  function AdminOption($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getName() {
    return($this->name);
    }

  function getAdmin() {
    return($this->admin);
    }

  function getValue() {
    return($this->value);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setName($name) {
    $this->name = $name;
    }

  function setAdmin($admin) {
    $this->admin = $admin;
    }

  function setValue($value) {
    $this->value = $value;
    }

  }

?>
