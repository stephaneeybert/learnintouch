<?php

// Access a table row through a database result.
// This class in an interface.

// Database result types; results can be returned as an indexed array, an associative array, or both.
// The latter (both) is the default.

define('DB_RESULT_ARRAY_NUM'  , 1);
define('DB_RESULT_ARRAY_ASSOC', 2);
define('DB_RESULT_ARRAY_BOTH' , 3);

class DataResult {

  // Private
  var $dataSource;

  // Private
  var $resultId;

  // Protected
  var $currentRow;

  // Protected
  var $errorMessage;

  // A constructor
  // The database result is passed to this class
  function DataResult($dataSource, $resultId) {
    $this->dataSource = $dataSource;

    $this->resultId = $resultId;
  }

  // Clear the database result
  function clear() {
    die('Method <b>clear</b> of class <b>DataResult</b> is not implemented.');
  }

  // Get the database access object
  function getDataSource() {
    return($this->dataSource);
  }

  // Get the database  result
  function getResultId() {
    return($this->resultId);
  }

  // Check if the database query has been succesfull
  // That is, if it returned a database result
  function isSuccessful() {
    return($this->resultId != 0);
  }

  // Get the error message
  function getErrorMessage() {
    die('Method <b>getErrorMessage</b> of class <b>QueryResult</b> is not implemented');
  }

  // Get a row from the database table
  function getRow($index, $type = DB_RESULT_ARRAY_BOTH) {
    die('Method <b>getRow</b> of class <b>QueryResult</b> is not implemented.');
  }

  // Get the number of rows affected by the last query
  function getRowCount() {
    die('Method <b>getRowCount</b> of class <b>QueryResult</b> is not implemented.');
  }

  // Get the number of fields affected by the last query
  function getFieldCount() {
    die('Method <b>getFieldCount</b> of class <b>QueryResult</b> is not implemented.');
  }

  // Get the name of a field
  function getFieldName($index) {
    die('Method <b>getFieldName</b> of class <b>QueryResult</b> is not implemented.');
  }

}

?>
