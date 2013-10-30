<?php

class RssFeed {

  var $id;

  function RssFeed($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function setId($id) {
    $this->id = $id;
  }

}

?>
