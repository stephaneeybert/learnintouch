<?php

class NavbarLanguage {

  var $id;
  var $language;
  var $navbarId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getLanguage() {
    return($this->language);
  }

  function getNavbarId() {
    return($this->navbarId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function setNavbarId($navbarId) {
    $this->navbarId = $navbarId;
  }

}

?>
