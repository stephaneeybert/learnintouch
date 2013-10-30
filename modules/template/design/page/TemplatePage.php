<?php

// It is a set of css properties.

class TemplatePage {

  var $id;
  var $systemPage;
  var $templateModelId;

  function TemplatePage($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getSystemPage() {
    return($this->systemPage);
    }

  function getTemplateModelId() {
    return($this->templateModelId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setSystemPage($systemPage) {
    $this->systemPage = $systemPage;
    }

  function setTemplateModelId($templateModelId) {
    $this->templateModelId = $templateModelId;
    }

  }

?>
