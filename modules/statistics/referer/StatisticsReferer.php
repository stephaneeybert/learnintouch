<?php

class StatisticsReferer {

  var $id;
  var $name;
  var $description;
  var $url;

  function __construct($id = '') {
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

  function getUrl() {
    return($this->url);
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

  function setUrl($url) {
    $this->url = $url;
  }

}

?>
