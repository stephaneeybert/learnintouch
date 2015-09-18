<?php

class Client {

  var $id;
  var $name;
  var $description;
  var $image;
  var $url;
  var $listOrder;

  function Client($id = '') {
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
    return($this->image);
  }

  function getUrl() {
    return($this->url);
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

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
