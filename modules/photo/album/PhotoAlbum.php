<?php

class PhotoAlbum {

  var $id;
  var $name;
  var $folderName;
  var $event;
  var $location;
  var $publicationDate;
  var $price;
  var $hide;
  var $noSlideShow;
  var $noZoom;
  var $listOrder;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getFolderName() {
    return($this->folderName);
  }

  function getEvent() {
    return($this->event);
  }

  function getLocation() {
    return($this->location);
  }

  function getPublicationDate() {
    return($this->publicationDate);
  }

  function getPrice() {
    return($this->price);
  }

  function getHide() {
    return($this->hide);
  }

  function getNoSlideShow() {
    return($this->noSlideShow);
  }

  function getNoZoom() {
    return($this->noZoom);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setFolderName($folderName) {
    $this->folderName = $folderName;
  }

  function setEvent($event) {
    $this->event = $event;
  }

  function setLocation($location) {
    $this->location = $location;
  }

  function setPublicationDate($publicationDate) {
    $this->publicationDate = $publicationDate;
  }

  function setPrice($price) {
    $this->price = $price;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

  function setNoSlideShow($noSlideShow) {
    $this->noSlideShow = $noSlideShow;
  }

  function setNoZoom($noZoom) {
    $this->noZoom = $noZoom;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
