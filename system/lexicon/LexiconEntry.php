<?php

class LexiconEntry {

  var $id;
  var $name;
  var $explanation;
  var $image;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getExplanation() {
    return($this->explanation);
  }

  function getImage() {
    return($this->image);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setExplanation($explanation) {
    $this->explanation = $explanation;
  }

  function setImage($image) {
    $this->image = $image;
  }

}

?>
