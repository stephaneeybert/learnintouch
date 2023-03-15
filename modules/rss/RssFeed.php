<?php

class RssFeed {

  var $id;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function setId($id) {
    $this->id = $id;
  }

}

?>
