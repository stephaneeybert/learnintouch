<?php

class LinkDao extends Dao {

  var $tableName;

  function LinkDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50),
description varchar(255),
image varchar(255),
url varchar(255),
category_id int unsigned,
index (category_id), foreign key (category_id) references link_category(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $image, $url, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$image', '$url', $categoryId, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $image, $url, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', image = '$image', url = '$url', category_id = $categoryId, list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY category_id, list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryId($categoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryIdOrderById($categoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($categoryId) {
    $sqlStatement = "SELECT count(distinct l1.id) as count FROM $this->tableName l1, $this->tableName l2 where l1.id != l2.id and l1.category_id = l2.category_id and l1.list_order = l2.list_order and l1.category_id = $categoryId";
    return($this->querySelect($sqlStatement));
  }

}

?>
