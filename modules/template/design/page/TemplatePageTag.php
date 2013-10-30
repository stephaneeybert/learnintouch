<?php

// It is a set of css properties.

class TemplatePageTag {

  var $id;
  var $templatePageId;
  var $templatePropertySetId;
  var $tagID;

  function TemplatePageTag($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getTemplatePageId() {
    return($this->templatePageId);
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

  function setTemplatePageId($templatePageId) {
    $this->templatePageId = $templatePageId;
    }

  function setTemplatePropertySetId($templatePropertySetId) {
    $this->templatePropertySetId = $templatePropertySetId;
    }

  function setTagID($tagID) {
    $this->tagID = $tagID;
    }

  }

?>
