<?php

class WebsiteOption {

  var $id;
  var $name;
  var $value;
  var $websiteId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getValue() {
    return($this->value);
  }

  function getWebsiteId() {
    return($this->websiteId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setValue($value) {
    $this->value = $value;
  }

  function setWebsiteId($websiteId) {
    $this->websiteId = $websiteId;
  }

}

?>
