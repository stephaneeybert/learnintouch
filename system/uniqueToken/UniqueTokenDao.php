<?php

class UniqueTokenDao extends Dao {

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
value varchar(50) not null,
unique (value),
creation_datetime datetime not null,
expiration_datetime datetime,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $value, $creationDateTime, $expirationDateTime) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$value', '$creationDateTime', '$expirationDateTime')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $value, $creationDateTime, $expirationDateTime) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', value = '$value', creation_datetime = '$creationDateTime', expiration_datetime = '$expirationDateTime' WHERE id = '$id'";
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

  function selectByNameAndValue($name, $value) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' AND value = '$value' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
