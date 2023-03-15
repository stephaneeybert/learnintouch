<?php

class Container {

  var $id;
  var $content;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getContent() {
    return($this->content);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setContent($content) {
    $this->content = $content;
  }

}

?>
