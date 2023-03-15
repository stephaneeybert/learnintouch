<?php

class Photo {

  var $id;
  var $reference;
  var $name;
  var $description;
  var $tags;
  var $comment;
  var $image;
  var $url;
  var $price;
  var $photoAlbum;
  var $photoFormatId;
  var $listOrder;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getReference() {
    return($this->reference);
  }

  function getName() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function getTags() {
    return($this->tags);
  }

  function getComment() {
    return($this->comment);
  }

  function getImage() {
    return($this->image) ;
  }

  function getUrl() {
    return($this->url);
  }

  function getPrice() {
    return(str_replace(',', '.', $this->price));
  }

  function getPhotoAlbum() {
    return($this->photoAlbum);
  }

  function getPhotoFormatId() {
    return($this->photoFormatId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setReference($reference) {
    $this->reference = $reference;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setTags($tags) {
    $this->tags = $tags;
  }

  function setComment($comment) {
    $this->comment = $comment;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setUrl($url) {
    $this->url = $url;
  }

  function setPrice($price) {
    $this->price = $price;
  }

  function setPhotoAlbum($photoAlbum) {
    $this->photoAlbum = $photoAlbum;
  }

  function setPhotoFormatId($photoFormatId) {
    $this->photoFormatId = $photoFormatId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
