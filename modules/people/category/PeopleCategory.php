<?php

class PeopleCategory {

  var $id;
  var $name;
  var $description;
  var $listOrder;

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

  function getListOrder() {
    return($this->listOrder);
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

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
