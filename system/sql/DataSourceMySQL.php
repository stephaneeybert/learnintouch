<?php

// The functions managing the MySQL specific data source.

class DataSourceMySQL extends DataSource {

  function DataSourceMySQL($host = '', $databaseName) {
    $this->DataSource($host, $databaseName);
  }

  function connect($username, $password, $type = DB_NON_PERSISTENT) {
    // Connect to the databse
    if ($type == DB_PERSISTENT) {
      $this->setDbConnection(mysqli_pconnect($this->getHost(), $username, $password));
    } else {
      $this->setDbConnection(mysqli_connect($this->getHost(), $username, $password));
    }

    $this->selectDatabase();

    // Return the connection status
    return($this->isConnected());
  }

  function selectDatabase() {
    $success = mysqli_select_db($this->getDbConnection(), $this->getDatabaseName());

    return($success);
  }

  function disconnect() {
    // Close the database
    mysqli_close($this->getDbConnection());

    // Reset the database connection
    $this->setDbConnection(0);
  }

  function query($sqlStatement) {
    // Keep the sql statement for the error message
    $this->sqlStatement = $sqlStatement;

    // Create a result object
    $result = new DataResultMySQL($this, mysqli_query($this->getDbConnection(), $sqlStatement));

    return($result);
  }

  function getErrorMessage() {
    global $skipReportError;

    $sqlErrorMessage = $this->sqlStatement . "<br><br>" . mysqli_error($this->getDbConnection());

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
