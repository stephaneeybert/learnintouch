<?php

class DynpageNavmenu {

  var $id;
  var $parentId;

  function DynpageNavmenu($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getParentId() {
    return($this->parentId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setParentId($parentId) {
    $this->parentId = $parentId;
  }

}

?>
