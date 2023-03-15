<?php

class ElearningLessonModel {

  var $id;
  var $name;
  var $description;
  var $instructions;
  var $locked;

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

  function getInstructions() {
    return($this->instructions);
  }

  function getLocked() {
    return($this->locked);
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

  function setInstructions($instructions) {
    $this->instructions = $instructions;
  }

  function setLocked($locked) {
    $this->locked = $locked;
  }

}

?>
