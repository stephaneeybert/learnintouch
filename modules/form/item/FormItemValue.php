<?php

class FormItemValue {

  var $id;
  var $value;
  var $text;
  var $formItemId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getValue() {
    return($this->value);
  }

  function getText() {
    return($this->text);
  }

  function getFormItemId() {
    return($this->formItemId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setValue($value) {
    $this->value = $value;
  }

  function setText($text) {
    $this->text= $text;
  }

  function setFormItemId($formItemId) {
    $this->formItemId = $formItemId;
  }

}

?>
