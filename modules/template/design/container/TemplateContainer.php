<?php

// It is a block of content in a model.
// The content is a list elements.

class TemplateContainer {

  var $id;
  var $templateModelId;
  var $row;
  var $cell;
  var $templatePropertySetId;

  function TemplateContainer($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getTemplateModelId() {
    return($this->templateModelId);
  }

  function getRow() {
    return($this->row);
  }

  function getCell() {
    return($this->cell);
  }

  function getTemplatePropertySetId() {
    return($this->templatePropertySetId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setTemplateModelId($templateModelId) {
    $this->templateModelId = $templateModelId;
  }

  function setRow($row) {
    $this->row = $row;
  }

  function setCell($cell) {
    $this->cell = $cell;
  }

  function setTemplatePropertySetId($templatePropertySetId) {
    $this->templatePropertySetId = $templatePropertySetId;
  }

}

?>
