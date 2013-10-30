<?php

class NavmenuItem {

  var $id;
  var $name;
  var $image;
  var $imageOver;
  var $url;
  var $blankTarget;
  var $description;
  var $hide;
  var $templateModelId;
  var $listOrder;
  var $parentNavmenuItemId;

  function NavmenuItem($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getName() {
    return($this->name);
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

  function getDescription() {
    return($this->description);
    }

  function getHide() {
    return($this->hide);
    }

  function getTemplateModelId() {
    return($this->templateModelId);
    }

  function getListOrder() {
    return($this->listOrder);
    }

  function getParentNavmenuItemId() {
    return($this->parentNavmenuItemId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setName($name) {
    $this->name = $name;
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

  function setDescription($description) {
    $this->description = $description;
    }

  function setHide($hide) {
    $this->hide = $hide;
    }

  function setTemplateModelId($templateModelId) {
    $this->templateModelId = $templateModelId;
    }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
    }

  function setParentNavmenuItemId($parentNavmenuItemId) {
    $this->parentNavmenuItemId = $parentNavmenuItemId;
    }

  }

?>
