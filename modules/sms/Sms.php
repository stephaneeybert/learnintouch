<?php

class Sms {

  var $id;
  var $body;
  var $description;
  var $adminId;
  var $categoryId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getBody() {
    return($this->body);
  }

  function getDescription() {
    return($this->description);
  }

  function getAdminId() {
    return($this->adminId);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setBody($body) {
    $this->body = $body;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

}

?>
