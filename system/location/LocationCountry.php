<?php

class LocationCountry {

  var $id;
  var $code;
  var $name;
  var $listOrder;

  function LocationCountry($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getCode() {
    return($this->code);
  }

  function getName() {
    return($this->name);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setCode($code) {
    $this->code = $code;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
