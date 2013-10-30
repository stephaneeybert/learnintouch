<?php

class TemplateModel {

  var $id;
  var $name;
  var $description;
  var $modelType;
  var $parentId;
  var $templatePropertySetId;
  var $innerTemplatePropertySetId;

  function TemplateModel($id = '') {
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

  function getModelType() {
    return($this->modelType);
  }

  function getParentId() {
    return($this->parentId);
  }

  function getTemplatePropertySetId() {
    return($this->templatePropertySetId);
  }

  function getInnerTemplatePropertySetId() {
    return($this->innerTemplatePropertySetId);
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

  function setModelType($modelType) {
    $this->modelType = $modelType;
  }

  function setParentId($parentId) {
    $this->parentId = $parentId;
  }

  function setTemplatePropertySetId($templatePropertySetId) {
    $this->templatePropertySetId = $templatePropertySetId;
  }

  function setInnerTemplatePropertySetId($innerTemplatePropertySetId) {
    $this->innerTemplatePropertySetId = $innerTemplatePropertySetId;
  }

}

?>
