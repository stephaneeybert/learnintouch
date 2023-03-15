<?php

class StatisticsRefererDao extends Dao {

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
name varchar(50),
description varchar(255),
url varchar(255) not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $url) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$url')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $url) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', url = '$url' WHERE id = '$id'";
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

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByUrl($url) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE url = '$url' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
