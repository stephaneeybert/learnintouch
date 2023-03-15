<?php

class ContactStatus {

  var $id;
  var $listOrder;
  var $name;
  var $description;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getName() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

}

?>
