<?php

class NewsStoryImage {

  var $id;
  var $image;
  var $description;
  var $listOrder;
  var $newsStoryId;

  function NewsStoryImage($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getImage() {
    return($this->image) ;
  }

  function getDescription() {
    return($this->description);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getNewsStoryId() {
    return($this->newsStoryId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setNewsStoryId($newsStoryId) {
    $this->newsStoryId = $newsStoryId;
  }

}

?>
