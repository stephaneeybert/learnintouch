<?php

class TemplateElement {

  var $id;
  var $elementType;
  var $objectId;
  var $templateContainerId;
  var $listOrder;
  var $hide;

  function TemplateElement($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getElementType() {
    return($this->elementType);
  }

  function getObjectId() {
    return($this->objectId);
  }

  function getTemplateContainerId() {
    return($this->templateContainerId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getHide() {
    return($this->hide);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setElementType($elementType) {
    $this->elementType = $elementType;
  }

  function setObjectId($objectId) {
    $this->objectId = $objectId;
  }

  function setTemplateContainerId($templateContainerId) {
    $this->templateContainerId = $templateContainerId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

}

?>
