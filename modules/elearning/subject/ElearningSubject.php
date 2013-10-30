<?php

class ElearningSubject {

  var $id;
  var $name;
  var $description;

  function ElearningSubject($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getName() {
    return($this->name);
    }

  function getDescription() {
    return($this->description) ;
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

  }

?>
