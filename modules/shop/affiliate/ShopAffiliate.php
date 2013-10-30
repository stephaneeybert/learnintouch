<?php

class ShopAffiliate {

  var $id;
  var $userId;

  function ShopAffiliate($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getUserId() {
    return($this->userId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

}

?>
