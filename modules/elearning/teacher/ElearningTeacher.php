<?php

class ElearningTeacher {

  var $id;
  var $userId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getUserId() {
    return($this->userId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

}

?>
