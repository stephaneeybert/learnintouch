<?php

class SqlToolsDao extends Dao {

  function __construct($dataSource) {
    parent::__construct($dataSource);
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
