<?php

class Document {

  var $id;
  var $reference;
  var $description;
  var $file;
  var $hide;
  var $secured;
  var $categoryId;
  var $listOrder;

  function Document($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getReference() {
    return($this->reference);
  }

  function getDescription() {
    return($this->description);
  }

  function getFile() {
    return($this->file) ;
  }

  function getHide() {
    return($this->hide);
  }

  function getSecured() {
    return($this->secured);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setReference($reference) {
    $this->reference = $reference;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setFile($file) {
    $this->file = $file;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
