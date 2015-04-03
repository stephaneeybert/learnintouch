<?php

class DynpageNavmenuDao extends Dao {

  var $tableName;

  function DynpageNavmenuDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
parent_id int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($parentId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$parentId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $parentId) {
    $sqlStatement = "UPDATE $this->tableName SET parent_id = '$parentId' WHERE id = '$id'";
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

}

?>
