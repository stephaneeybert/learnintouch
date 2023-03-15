<?php

class FormValid {

  var $id;
  var $type;
  var $message;
  var $boundary;
  var $formItemId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getType() {
    return($this->type);
  }

  function getMessage() {
    return($this->message);
  }

  function getBoundary() {
    return($this->boundary);
  }

  function getFormItemId() {
    return($this->formItemId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setType($type) {
    $this->type = $type;
  }

  function setMessage($message) {
    $this->message = $message;
  }

  function setBoundary($boundary) {
    $this->boundary = $boundary;
  }

  function setFormItemId($formItemId) {
    $this->formItemId = $formItemId;
  }

}

?>
