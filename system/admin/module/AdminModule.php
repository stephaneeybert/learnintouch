<?php

class AdminModule {

  var $id;
  var $module;
  var $admin;

  function __construct($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getModule() {
    return($this->module);
    }

  function getAdmin() {
    return($this->admin);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setModule($module) {
    $this->module = $module;
    }

  function setAdmin($admin) {
    $this->admin = $admin;
    }

  }

?>
