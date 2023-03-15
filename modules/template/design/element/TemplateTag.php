<?php

// It is a set of css properties.

class TemplateTag {

  var $id;
  var $templateElementId;
  var $templatePropertySetId;
  var $tagID;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getTemplateElementId() {
    return($this->templateElementId);
  }

  function getTemplatePropertySetId() {
    return($this->templatePropertySetId);
  }

  function getTagID() {
    return($this->tagID);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setTemplateElementId($templateElementId) {
    $this->templateElementId = $templateElementId;
  }

  function setTemplatePropertySetId($templatePropertySetId) {
    $this->templatePropertySetId = $templatePropertySetId;
  }

  function setTagID($tagID) {
    $this->tagID = $tagID;
  }

}

?>
