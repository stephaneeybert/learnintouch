<?php

class Dynpage {

  var $id;
  var $name;
  var $description;
  var $content;
  var $hide;
  var $garbage;
  var $listOrder;
  var $secured;
  var $parentId;
  var $adminId;

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

  function getContent() {
    return($this->content);
  }

  function getHide() {
    return($this->hide);
  }

  function getGarbage() {
    return($this->garbage);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getSecured() {
    return($this->secured);
  }

  function getParentId() {
    return($this->parentId);
  }

  function getAdminId() {
    return($this->adminId);
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

  function setContent($content) {
    $this->content = $content;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

  function setGarbage($garbage) {
    $this->garbage = $garbage;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

  function setParentId($parentId) {
    $this->parentId = $parentId;
  }

  function setAdminId($adminId) {
    $this->adminId = $adminId;
  }

}

?>
