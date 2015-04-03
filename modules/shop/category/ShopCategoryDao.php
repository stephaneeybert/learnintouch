<?php

class ShopCategoryDao extends Dao {

  var $tableName;

  function ShopCategoryDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50) not null,
description varchar(255),
list_order int unsigned not null,
parent_id int unsigned,
index (parent_id), foreign key (parent_id) references shop_category(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $listOrder, $parentCategoryId) {
    $parentCategoryId = LibString::emptyToNULL($parentCategoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$listOrder', $parentCategoryId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $listOrder, $parentCategoryId) {
    $parentCategoryId = LibString::emptyToNULL($parentCategoryId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', list_order = '$listOrder', parent_id = $parentCategoryId WHERE id = '$id'";
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

  function selectByParentCategoryId($parentCategoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id = '$parentCategoryId' OR (parent_id IS NULL AND '$parentCategoryId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByParentCategoryIdOrderById($parentCategoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id = '$parentCategoryId' OR (parent_id IS NULL AND '$parentCategoryId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($parentCategoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentCategoryId' OR (parent_id IS NULL AND '$parentCategoryId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($parentCategoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentCategoryId' OR (parent_id IS NULL AND '$parentCategoryId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($parentCategoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentCategoryId' OR (parent_id IS NULL AND '$parentCategoryId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($parentCategoryId) {
    $sqlStatement = "SELECT count(distinct sc1.id) as count FROM $this->tableName sc1, $this->tableName sc2 where sc1.id != sc2.id and sc1.parent_id = sc2.parent_id and sc1.list_order = sc2.list_order and sc1.parent_id = $parentCategoryId";
    return($this->querySelect($sqlStatement));
  }

}

?>
