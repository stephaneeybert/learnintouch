<?php

class Flash {

  var $id;
  var $file;
  var $width;
  var $height;
  var $bgcolor;
  var $wddx;

  function Flash($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getFile() {
    return($this->file);
  }

  function getWidth() {
    return($this->width);
  }

  function getHeight() {
    return($this->height);
  }

  function getBgcolor() {
    return($this->bgcolor);
  }

  function getWddx() {
    return($this->wddx);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setFile($file) {
    $this->file = $file;
  }

  function setWidth($width) {
    $this->width = $width;
  }

  function setHeight($height) {
    $this->height = $height;
  }

  function setBgcolor($bgcolor) {
    $this->bgcolor = $bgcolor;
  }

  function setWddx($wddx) {
    $this->wddx = $wddx;
  }

}

?>
