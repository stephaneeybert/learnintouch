<?php

class NewsEditor {

  var $id;
  var $adminId;

  function NewsEditor($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getAdminId() {
    return($this->adminId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

}

?>
