<?php

class ShopOrderItemDao extends Dao {

  var $tableName;

  function __construct($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50),
short_description varchar(255),
reference varchar(30),
price varchar(12) not null,
vat_rate varchar(5),
shipping_fee varchar(10),
quantity int(2) unsigned not null,
is_gift boolean not null,
options varchar(255),
shop_order_id int unsigned not null,
index (shop_order_id), foreign key (shop_order_id) references shop_order(id),
shop_item_id int unsigned,
index (shop_item_id), foreign key (shop_item_id) references shop_item(id),
image_url varchar(255),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $shortDescription, $reference, $price, $vatRate, $shippingFee, $quantity, $isGift, $options, $shopOrderId, $shopItemId, $imageUrl) {
    $shopItemId = LibString::emptyToNULL($shopItemId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$shortDescription', '$reference', '$price', '$vatRate', '$shippingFee', '$quantity', '$isGift', '$options', '$shopOrderId', $shopItemId, '$imageUrl')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $shortDescription, $reference, $price, $vatRate, $shippingFee, $quantity, $isGift, $options, $shopOrderId, $shopItemId, $imageUrl) {
    $shopItemId = LibString::emptyToNULL($shopItemId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', short_description = '$shortDescription', reference = '$reference', price = '$price', vat_rate = '$vatRate', shipping_fee = '$shippingFee', quantity = '$quantity', is_gift = '$isGift', options = '$options', shop_order_id = '$shopOrderId', shop_item_id = $shopItemId, image_url = '$imageUrl' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE lower(reference) LIKE lower('%$searchPattern%') OR lower(name) LIKE lower('%$searchPattern%') OR lower(short_description) LIKE lower('%$searchPattern%') ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectByShopOrderId($shopOrderId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_order_id = '$shopOrderId' ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectByShopItemId($shopItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_item_id = '$shopItemId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByShopOrderIdAndShopItemId($shopOrderId, $shopItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_order_id = '$shopOrderId' AND (shop_item_id = '$shopItemId' OR (shop_item_id IS NULL AND '$shopItemId' < '1')) LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
