<?php

// The functions managing the MySQL specific data source.

class DataSourceMySQL extends DataSource {

  function DataSourceMySQL($host = '', $databaseName) {
    $this->DataSource($host, $databaseName);
  }

  function connect($username, $password, $type = DB_NON_PERSISTENT) {
    // Connect to the databse
    if ($type == DB_PERSISTENT) {
      $this->setDbConnection(mysql_pconnect($this->getHost(), $username, $password));
    } else {
      $this->setDbConnection(mysql_connect($this->getHost(), $username, $password));
    }

    $this->selectDatabase();

    // Return the connection status
    return($this->isConnected());
  }

  function selectDatabase() {
    $success = mysql_select_db($this->getDatabaseName(), $this->getDbConnection());

    return($success);
  }

  function disconnect() {
    // Close the database
    mysql_close($this->getDbConnection());

    // Reset the database connection
    $this->setDbConnection(0);
  }

  function query($sqlStatement) {
    // Keep the sql statement for the error message
    $this->sqlStatement = $sqlStatement;

    // Create a result object
    $result = new DataResultMySQL($this, mysql_query($sqlStatement, $this->getDbConnection()));

    return($result);
  }

  function getErrorMessage() {
    global $skipReportError;

    $sqlErrorMessage = $this->sqlStatement . "<br><br>" . mysql_error($this->getDbConnection());

    if (!isset($skipReportError)) {
      reportError("The sql statement failed. " . $sqlErrorMessage);
    }

    return($sqlErrorMessage);
  }

  function listTables() {
    $result = new DataResultMySQL($this, mysql_list_tables($this->getDatabaseName()));

    return($result);
  }

  function getLastInsertId() {
    $id = mysql_insert_id($this->getDbConnection());

    return($id);
  }

}

?>
