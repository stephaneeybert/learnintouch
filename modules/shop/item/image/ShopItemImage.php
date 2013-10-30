<?php

class ShopItemImage {

  var $id;
  var $image;
  var $description;
  var $listOrder;
  var $shopItemId;

  function ShopItemImage($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getImage() {
    return($this->image) ;
  }

  function getDescription() {
    return($this->description);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getShopItemId() {
    return($this->shopItemId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setShopItemId($shopItemId) {
    $this->shopItemId = $shopItemId;
  }

}

?>
