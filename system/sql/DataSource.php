<?php

// Query a database and return a database result.
// This class in an interface.
// It is not supposed to be instanciated.
// It describes the services implemented by its extended classes.

// Database connection methods; either persistent or non-persistent.
// The latter (non persistent) is the default one.
define('DB_PERSISTENT', true);
define('DB_NON_PERSISTENT', false);

class DataSource {

  // Private
  // The host name
  var $host;

  // Private
  // The name of the database
  var $databaseName;

  // Private
  // The database connection
  var $dbConnection;

  // The sql statement
  var $sqlStatement;

  function DataSource($host, $databaseName) {
    $this->host = $host;
    $this->databaseName = $databaseName;
    $this->dbConnection = 0;
    $this->sqlStatement = '';
    }

  // Get the host name
  function getHost() {
    return($this->host);
    }

  // Get the database name
  function getDatabaseName() {
    return($this->databaseName);
    }

  // Get the database connection
  function getDbConnection() {
    return($this->dbConnection);
    }

  // Check if a connection is established with the database
  function isConnected() {
    return($this->dbConnection != 0);
    }

  // Connect to the database
  function connect($user, $password) {
    die('Method <b>connect</b> of class <b>DataSource</b> is not implemented.');
    }

  function selectDatabase() {
    die('Method <b>selectDatabase</b> of class <b>DataSource</b> is not implemented.');
    }

  // Disconnect from the database
  function disconnect() {
    die('Method <b>disconnect</b> of class <b>DataSource</b> is not implemented.');
    }

  // Protected
  // Connect to the database using an existing connection
  // No new connection is created
  function setDbConnection($dbConnection) {
    $this->dbConnection = $dbConnection;
    }

  // Query the database and return the result
  function query($sqlStatement) {
    die('Method <b>query</b> of class <b>DataSource</b> is not implemented.');
    }

  // Get the error from the last query if any
  function getErrorMessage() {
    die('Method <b>getError</b> of class <b>DataSource</b> is not implemented.');
    }

  function listTables() {
    die('Method <b>listTables</b> of class <b>DataSource</b> is not implemented.');
    }

  function getLastInsertId() {
    die('Method <b>getLastInsertId</b> of class <b>DataSource</b> is not implemented.');
    }

  }

?>
