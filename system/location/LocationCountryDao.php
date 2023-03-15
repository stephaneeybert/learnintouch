<?php

class LocationCountryDao extends Dao {

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
code varchar(4) not null,
unique (code),
name varchar(50) not null,
list_order int unsigned not null,
index (list_order, name),
index (code),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($code, $name, $listOrder) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$code', '$name', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $code, $name, $listOrder) {
    $sqlStatement = "UPDATE $this->tableName SET code = '$code', name = '$name', list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY list_order, name";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCode($code) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE code = '$code'";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name'";
    return($this->querySelect($sqlStatement));
  }

}

?>
