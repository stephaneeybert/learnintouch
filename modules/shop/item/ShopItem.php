<?php

class ShopItem {

  var $id;
  var $name;
  var $shortDescription;
  var $longDescription;
  var $reference;
  var $weight;
  var $price;
  var $vatRate;
  var $shippingFee;
  var $categoryId;
  var $added;
  var $url;
  var $listOrder;
  var $lastModified;
  var $available;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getShortDescription() {
    return($this->shortDescription);
  }

  function getLongDescription() {
    return($this->longDescription);
  }

  function getReference() {
    return($this->reference);
  }

  function getWeight() {
    return($this->weight);
  }

  function getPrice() {
    return(str_replace(',', '.', $this->price));
  }

  function getVatRate() {
    return($this->vatRate);
  }

  function getShippingFee() {
    return(str_replace(',', '.', $this->shippingFee));
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function getUrl() {
    return($this->url);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getHide() {
    return($this->hide);
  }

  function getAdded() {
    return($this->added);
  }

  function getLastModified() {
    return($this->lastModified);
  }

  function getAvailable() {
    return($this->available);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setShortDescription($shortDescription) {
    $this->shortDescription = $shortDescription;
  }

  function setLongDescription($longDescription) {
    $this->longDescription = $longDescription;
  }

  function setReference($reference) {
    $this->reference = $reference;
  }

  function setWeight($weight) {
    $this->weight = $weight;
  }

  function setPrice($price) {
    $this->price = $price;
  }

  function setVatRate($vatRate) {
    $this->vatRate = $vatRate;
  }

  function setShippingFee($shippingFee) {
    $this->shippingFee = $shippingFee;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setUrl($url) {
    $this->url = $url;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setHide($hide) {
    $this->hide = $hide;
  }

  function setAdded($added) {
    $this->added = $added;
  }

  function setLastModified($lastModified) {
    $this->lastModified = $lastModified;
  }

  function setAvailable($available) {
    $this->available = $available;
  }

}

?>
