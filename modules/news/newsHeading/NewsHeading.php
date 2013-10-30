<?php

class NewsHeading {

  var $id;
  var $name;
  var $description;
  var $image;
  var $listOrder;
  var $newsPublicationId;

  function NewsHeading($id = '') {
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

  function getImage() {
    return($this->image);
  }

  function getNewsPublicationId() {
    return($this->newsPublicationId);
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

  function setImage($image) {
    $this->image = $image;
  }

  function setNewsPublicationId($newsPublicationId) {
    $this->newsPublicationId = $newsPublicationId;
  }

}

?>
