<?php

class NavmenuLanguage {

  var $id;
  var $language;
  var $navmenuId;
  var $navmenuItemId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getLanguage() {
    return($this->language);
  }

  function getNavmenuId() {
    return($this->navmenuId);
  }

  function getNavmenuItemId() {
    return($this->navmenuItemId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function setNavmenuId($navmenuId) {
    $this->navmenuId = $navmenuId;
  }

  function setNavmenuItemId($navmenuItemId) {
    $this->navmenuItemId = $navmenuItemId;
  }

}

?>
