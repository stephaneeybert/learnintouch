<?php

class TemplateElementLanguage {

  var $id;
  var $language;
  var $objectId;
  var $templateElementId;

  function TemplateElementLanguage($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getLanguage() {
    return($this->language);
  }

  function getObjectId() {
    return($this->objectId);
  }

  function getTemplateElementId() {
    return($this->templateElementId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function setObjectId($objectId) {
    $this->objectId = $objectId;
  }

  function setTemplateElementId($templateElementId) {
    $this->templateElementId = $templateElementId;
  }

}

?>
