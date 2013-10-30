<?php

class ShopCategory {

  var $id;
  var $name;
  var $description;
  var $listOrder;
  var $parentCategoryId;

  function ShopCategory($id = '') {
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

  function getParentCategoryId() {
    return($this->parentCategoryId);
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

  function setParentCategoryId($parentCategoryId) {
    $this->parentCategoryId = $parentCategoryId;
  }

}

?>
