<?php

class PeopleCategoryDao extends Dao {

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
name varchar(50) not null,
description varchar(255),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $listOrder) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $listOrder) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', list_order = '$listOrder' WHERE id = '$id'";
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

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectAllOrderById() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order = '$listOrder' ORDER BY list_order DESC";
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

  function countDuplicateListOrderRows() {
    $sqlStatement = "SELECT count(distinct pc1.id) as count FROM $this->tableName pc1, $this->tableName pc2 where pc1.id != pc2.id and pc1.list_order = pc2.list_order";
    return($this->querySelect($sqlStatement));
  }

}

?>
