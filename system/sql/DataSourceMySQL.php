<?php

// The functions managing the MySQL specific data source.

class DataSourceMySQL extends DataSource {

  function DataSourceMySQL($host = '', $port = '', $databaseName) {
    $this->DataSource($host, $port, $databaseName);
  }

  function connect($username, $password, $type = DB_NON_PERSISTENT) {
    if ($type == DB_PERSISTENT) {
      $this->setDbConnection(mysqli_connect("p:". $this->getHost(), $username, $password, $this->getDatabaseName(), $this->getPort()));
    } else {
      $this->setDbConnection(mysqli_connect($this->getHost(), $username, $password, $this->getDatabaseName(), $this->getPort()));
    }

    $this->selectDatabase();

    return($this->isConnected());
  }

  function selectDatabase() {
    $success = mysqli_select_db($this->getDbConnection(), $this->getDatabaseName());

    return($success);
  }

  function disconnect() {
    mysqli_close($this->getDbConnection());

    $this->setDbConnection(0);
  }

  function query($sqlStatement) {
    $this->sqlStatement = $sqlStatement;

    $result = new DataResultMySQL($this, mysqli_query($this->getDbConnection(), $sqlStatement));

    return($result);
  }

  function getErrorMessage() {
    global $skipReportError;

    $sqlErrorMessage = $this->sqlStatement . "<br><br>" . mysqli_error($this->getDbConnection());

    if (mysqli_connect_errno()) {
      reportError("Failed to connect to MySQL: " . mysqli_connect_error() . $sqlErrorMessage);
    }

    if (!isset($skipReportError)) {
      reportError("The sql statement failed. " . $sqlErrorMessage);
    }

    return($sqlErrorMessage);
  }

  function listTables() {
    $result = new DataResultMySQL($this, mysqli_list_tables($this->getDatabaseName()));

    return($result);
  }

  function getLastInsertId() {
    $id = mysqli_insert_id($this->getDbConnection());

    return($id);
  }

}

?>
