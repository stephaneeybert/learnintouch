<?php

class FormItem {

  var $id;
  var $type;
  var $name;
  var $text;
  var $help;
  var $defaultValue;
  var $size;
  var $maxlength;
  var $listOrder;
  var $inMailAddress;
  var $mailListId;
  var $formId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getType() {
    return($this->type);
  }

  function getName() {
    return($this->name);
  }

  function getText() {
    return($this->text);
  }

  function getHelp() {
    return($this->help);
  }

  function getDefaultValue() {
    return($this->defaultValue);
  }

  function getSize() {
    return($this->size);
  }

  function getMaxlength() {
    return($this->maxlength);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getInMailAddress() {
    return($this->inMailAddress);
  }

  function getMailListId() {
    return($this->mailListId);
  }

  function getFormId() {
    return($this->formId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setType($type) {
    $this->type = $type;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setText($text) {
    $this->text = $text;
  }

  function setHelp($help) {
    $this->help = $help;
  }

  function setDefaultValue($defaultValue) {
    $this->defaultValue = $defaultValue;
  }

  function setSize($size) {
    $this->size = $size;
  }

  function setMaxlength($maxlength) {
    $this->maxlength = $maxlength;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setInMailAddress($inMailAddress) {
    $this->inMailAddress = $inMailAddress;
  }

  function setMailListId($mailListId) {
    $this->mailListId = $mailListId;
  }

  function setFormId($formId) {
    $this->formId = $formId;
  }

}

?>
