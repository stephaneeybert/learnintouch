<?php

class BackupDao extends Dao {

  function BackupDao($dataSource) {
    $this->Dao($dataSource);
  }

  function showCreateTable($tableName) {
    $sqlStatement = "SHOW CREATE TABLE $tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectAllFromTable($tableName) {
    $sqlStatement = "SELECT * FROM $tableName";
    return($this->querySelect($sqlStatement));
  }

}

?>
