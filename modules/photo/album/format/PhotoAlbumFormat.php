<?php

class PhotoAlbumFormat {

  var $id;
  var $photoAlbumId;
  var $photoFormatId;
  var $price;

  function PhotoAlbumFormat($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getPhotoAlbumId() {
    return($this->photoAlbumId);
    }

  function getPhotoFormatId() {
    return($this->photoFormatId);
    }

  function getPrice() {
    return($this->price);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setPhotoAlbumId($photoAlbumId) {
    $this->photoAlbumId = $photoAlbumId;
    }

  function setPhotoFormatId($photoFormatId) {
    $this->photoFormatId = $photoFormatId;
    }

  function setPrice($price) {
    $this->price = $price;
    }

  }

?>
