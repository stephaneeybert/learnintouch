<?php

class NavmenuDao extends Dao {

  var $tableName;

  function NavmenuDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
hide boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($hide) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$hide')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $hide) {
    $sqlStatement = "UPDATE $this->tableName SET hide = '$hide' WHERE id = '$id'";
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
