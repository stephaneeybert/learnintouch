<?php

class Navbar {

  var $id;
  var $hide;

  function Navbar($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getHide() {
    return($this->hide);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

}

?>
