<?php

class ContactRefererDao extends Dao {

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
description text,
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($listOrder, $description) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$description', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $listOrder, $description) {
    $sqlStatement = "UPDATE $this->tableName SET description = '$description', list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectAllOrderById() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectFirst() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY list_order LIMIT 0, 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order = '$listOrder'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows() {
    $sqlStatement = "SELECT count(distinct cs1.id) as count FROM $this->tableName cs1, $this->tableName cs2 where cs1.id != cs2.id and cs1.list_order = cs2.list_order";
    return($this->querySelect($sqlStatement));
  }

}

?>
