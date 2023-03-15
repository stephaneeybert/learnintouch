<?php

class ElearningLessonHeading {

  var $id;
  var $name;
  var $content;
  var $listOrder;
  var $image;
  var $elearningLessonModelId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getContent() {
    return($this->content);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getImage() {
    return($this->image);
  }

  function getElearningLessonModelId() {
    return($this->elearningLessonModelId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setContent($content) {
    $this->content = $content;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setElearningLessonModelId($elearningLessonModelId) {
    $this->elearningLessonModelId = $elearningLessonModelId;
  }

}

?>
