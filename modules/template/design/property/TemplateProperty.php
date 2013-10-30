<?php

class TemplateProperty {

  var $id;
  var $name;
  var $value;
  var $templatePropertySetId;

  function TemplateProperty($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getName() {
    return($this->name);
    }

  function getValue() {
    return($this->value);
    }

  function getTemplatePropertySetId() {
    return($this->templatePropertySetId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setName($name) {
    $this->name = $name;
    }

  function setValue($value) {
    $this->value = $value;
    }

  function setTemplatePropertySetId($templatePropertySetId) {
    $this->templatePropertySetId = $templatePropertySetId;
    }

  }

?>
