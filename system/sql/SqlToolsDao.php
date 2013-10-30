<?php

class SqlToolsDao extends Dao {

  function SqlToolsDao($dataSource) {
    $this->Dao($dataSource);
  }

  function getDatabaseSize() {
    $sqlStatement = "SHOW TABLE STATUS";
    return($this->querySelect($sqlStatement));
  }

  function getDatabaseNames() {
    $sqlStatement = "SHOW DATABASES";
    return($this->querySelect($sqlStatement));
  }

  function performStatement($sqlStatement) {
    return($this->querySelect($sqlStatement));
  }

}

?>
