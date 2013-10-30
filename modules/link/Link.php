<?php

class Link {

  var $id;
  var $name;
  var $description;
  var $image;
  var $url;
  var $categoryId;
  var $listOrder;

  function Link($id = '') {
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

  function getImage() {
    return($this->image) ;
  }

  function getUrl() {
    return($this->url);
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

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setUrl($url) {
    $this->url = $url;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
