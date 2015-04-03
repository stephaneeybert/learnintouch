<?php

class ElearningScoringDao extends Dao {

  var $tableName;

  function ElearningScoringDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(255) not null,
description varchar(255),
required_score int unsigned,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $requiredScore) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$requiredScore')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $requiredScore) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', required_score = '$requiredScore' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName order by name";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
