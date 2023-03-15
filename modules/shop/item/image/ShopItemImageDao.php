<?php

class ShopItemImageDao extends Dao {

  var $tableName;

  function ShopItemImageDao($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
image varchar(255),
description varchar(255),
list_order int unsigned not null,
shop_item_id int unsigned not null,
index (shop_item_id), foreign key (shop_item_id) references shop_item(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($image, $description, $listOrder, $shopItemId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$image', '$description', '$listOrder', '$shopItemId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $image, $description, $listOrder, $shopItemId) {
    $sqlStatement = "UPDATE $this->tableName SET image = '$image', description = '$description', list_order = '$listOrder', shop_item_id = '$shopItemId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($shopItemId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($shopItemId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($shopItemId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByShopItemId($shopItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByShopItemIdOrderById($shopItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($shopItemId) {
    $sqlStatement = "SELECT count(distinct si1.id) as count FROM $this->tableName si1, $this->tableName si2 where si1.id != si2.id and si1.shop_item_id = si2.shop_item_id and si1.list_order = si2.list_order and si1.shop_item_id = $shopItemId";
    return($this->querySelect($sqlStatement));
  }

}

?>
