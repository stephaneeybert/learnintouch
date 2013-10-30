<?php

class ShopDiscount {

  var $id;
  var $discountCode;
  var $discountRate;
  var $shopAffiliateId;

  function ShopDiscount($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getDiscountCode() {
    return($this->discountCode);
  }

  function getDiscountRate() {
    return($this->discountRate);
  }

  function getShopAffiliateId() {
    return($this->shopAffiliateId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setDiscountCode($discountCode) {
    $this->discountCode = $discountCode;
  }

  function setDiscountRate($discountRate) {
    $this->discountRate = $discountRate;
  }

  function setShopAffiliateId($shopAffiliateId) {
    $this->shopAffiliateId = $shopAffiliateId;
  }

}

?>
