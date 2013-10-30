<?php

class Language {

  var $id;
  var $code;
  var $name;
  var $locale;
  var $image;

  function Language($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getCode() {
    return($this->code);
  }

  function getName() {
    return($this->name);
  }

  function getLocale() {
    return($this->locale);
  }

  function getImage() {
    return($this->image);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setCode($code) {
    $this->code = $code;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setLocale($locale) {
    $this->locale = $locale;
  }

  function setImage($image) {
    $this->image = $image;
  }

}

?>
