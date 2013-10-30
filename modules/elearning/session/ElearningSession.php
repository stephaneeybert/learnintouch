<?php

class ElearningSession {

  var $id;
  var $name;
  var $description;
  var $openDate;
  var $closeDate;
  var $closed;

  function ElearningSession($id = '') {
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

  function getOpenDate() {
    return($this->openDate);
  }

  function getCloseDate() {
    return($this->closeDate);
  }

  function getClosed() {
    return($this->closed) ;
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

  function setOpenDate($openDate) {
    $this->openDate = $openDate;
  }

  function setCloseDate($closeDate) {
    $this->closeDate = $closeDate;
  }

  function setClosed($closed) {
    $this->closed = $closed;
  }

}

?>
