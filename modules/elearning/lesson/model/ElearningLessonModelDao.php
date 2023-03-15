<?php

class ElearningLessonModelDao extends Dao {

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
name varchar(255) not null,
description varchar(255),
instructions text,
locked boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $instructions, $locked) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$instructions', '$locked')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $instructions, $locked) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', instructions = '$instructions', locked = '$locked' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
