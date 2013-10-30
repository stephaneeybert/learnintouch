<?php

class PhotoFormat {

  var $id;
  var $name;
  var $description;
  var $price;

  function PhotoFormat($id = '') {
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

  function getPrice() {
    return($this->price);
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

  function setPrice($price) {
    $this->price = $price;
  }

}

?>
