<?php

class ShopDiscountDao extends Dao {

  var $tableName;

  function ShopDiscountDao($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
discount_code varchar(12) not null,
discount_rate varchar(5) not null,
shop_affiliate_id int unsigned not null,
index (shop_affiliate_id), foreign key (shop_affiliate_id) references shop_affiliate(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($discountCode, $discountRate, $shopAffiliateId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$discountCode', '$discountRate', '$shopAffiliateId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $discountCode, $discountRate, $shopAffiliateId) {
    $sqlStatement = "UPDATE $this->tableName SET discount_code = '$discountCode', discount_rate = '$discountRate', shop_affiliate_id = '$shopAffiliateId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAffiliateId($shopAffiliateId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE shop_affiliate_id = '$shopAffiliateId'";
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

  function selectByDiscountCode($discountCode) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE discount_code = '$discountCode' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
