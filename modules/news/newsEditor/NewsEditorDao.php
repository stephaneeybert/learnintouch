<?php

class NewsEditorDao extends Dao {

  var $tableName;

  function NewsEditorDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
admin_id int unsigned not null,
index (admin_id), foreign key (admin_id) references admin(id),
unique (admin_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($adminId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$adminId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $adminId) {
    $sqlStatement = "UPDATE $this->tableName SET admin_id = '$adminId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT ne.* FROM $this->tableName ne, " . DB_TABLE_ADMIN . " a WHERE ne.admin_id = a.id ORDER BY a.firstname, a.lastname";
    return($this->querySelect($sqlStatement));
  }

  function selectByAdminId($adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE admin_id = '$adminId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
