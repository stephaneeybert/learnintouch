<?php

class AdminOptionDao extends Dao {

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
admin_id int unsigned not null,
index (admin_id), foreign key (admin_id) references admin(id),
value varchar(20),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $admin, $value) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$admin', '$value')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $admin, $value) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', admin_id = '$admin', value = '$value' WHERE id = '$id'";
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
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name'";
    return($this->querySelect($sqlStatement));
  }

  function selectByNameAndAdmin($name, $adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' AND admin_id = '$adminId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAdmin($adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE admin_id = '$adminId' ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

}

?>
