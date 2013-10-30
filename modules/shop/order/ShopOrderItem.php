<?php

class ShopOrderItem {

  var $id;
  var $name;
  var $shortDescription;
  var $reference;
  var $price;
  var $vatRate;
  var $shippingFee;
  var $quantity;
  var $isGift;
  var $options;
  var $shopOrderId;
  var $shopItemId;
  var $imageUrl;

  function ShopOrderItem($id = '') {
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

  function getReference() {
    return($this->reference);
  }

  function getPrice() {
    return($this->price);
  }

  function getVatRate() {
    return($this->vatRate);
  }

  function getShippingFee() {
    return($this->shippingFee);
  }

  function getQuantity() {
    return($this->quantity);
  }

  function getIsGift() {
    return($this->isGift);
  }

  function getOptions() {
    return($this->options);
  }

  function getShopOrderId() {
    return($this->shopOrderId);
  }

  function getShopItemId() {
    return($this->shopItemId);
  }

  function getImageUrl() {
    return($this->imageUrl);
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

  function setReference($reference) {
    $this->reference = $reference;
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

  function setQuantity($quantity) {
    $this->quantity = $quantity;
  }

  function setIsGift($isGift) {
    $this->isGift = $isGift;
  }

  function setOptions($options) {
    $this->options = $options;
  }

  function setShopOrderId($shopOrderId) {
    $this->shopOrderId = $shopOrderId;
  }

  function setShopItemId($shopItemId) {
    $this->shopItemId = $shopItemId;
  }

  function setImageUrl($imageUrl) {
    $this->imageUrl = $imageUrl;
  }

}

?>
