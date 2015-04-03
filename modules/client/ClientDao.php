<?php

class ClientDao extends Dao {

  var $tableName;

  function ClientDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

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
image varchar(255),
url varchar(255),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $image, $url) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$image', '$url')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $image, $url) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', image = '$image', url = '$url' WHERE id = '$id'";
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

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

}

?>
