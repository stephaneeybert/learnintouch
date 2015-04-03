<?php

class AdminModuleDao extends Dao {

  var $tableName;

  function AdminModuleDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
module varchar(50) not null,
admin_id int unsigned not null,
index (admin_id), foreign key (admin_id) references admin(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($module, $admin) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$module', '$admin')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $module, $admin) {
    $sqlStatement = "UPDATE $this->tableName SET module = '$module', admin_id = '$admin' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY module";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByModule($module) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE module = '$module'";
    return($this->querySelect($sqlStatement));
  }

  function selectByModuleAndAdmin($module, $adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE module = '$module' AND admin_id = '$adminId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAdmin($adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE admin_id = '$adminId' ORDER BY module";
    return($this->querySelect($sqlStatement));
  }

}

?>
