<?php

class NavlinkItem {

  var $id;
  var $name;
  var $description;
  var $image;
  var $imageOver;
  var $url;
  var $blankTarget;
  var $language;
  var $templateModelId;
  var $navlinkId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getText() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function getImage() {
    return($this->image);
  }

  function getImageOver() {
    return($this->imageOver);
  }

  function getUrl() {
    return($this->url);
  }

  function getBlankTarget() {
    return($this->blankTarget);
  }

  function getLanguage() {
    return($this->language);
  }

  function getTemplateModelId() {
    return($this->templateModelId);
  }

  function getNavlinkId() {
    return($this->navlinkId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setText($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setImageOver($imageOver) {
    $this->imageOver = $imageOver;
  }

  function setUrl($url) {
    $this->url = $url;
  }

  function setBlankTarget($blankTarget) {
    $this->blankTarget = $blankTarget;
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function setTemplateModelId($templateModelId) {
    $this->templateModelId = $templateModelId;
  }

  function setNavlinkId($navlinkId) {
    $this->navlinkId = $navlinkId;
  }

}

?>
