<?php

class ElearningScoring {

  var $id;
  var $name;
  var $description;
  var $requiredScore;

  function ElearningScoring($id = '') {
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

  function getRequiredScore() {
    return($this->requiredScore) ;
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

  function setRequiredScore($requiredScore) {
    $this->requiredScore = $requiredScore;
  }

}

?>
