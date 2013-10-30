<?php

// The Dao class is the Data Access Object class.
// It is the base class offering the common functionality to all dao classes with a sub class for each table of the database.
// Each database table keeps hold of its data source.

class Dao {

  // Private
  var $dataSource;

  // The possible error message
  var $errorMessage;

  function Dao($dataSource) {
    $this->dataSource = $dataSource;
  }

  // Run a database query
  function querySelect($sqlStatement) {
    if (!$this->dataSource) {
      return(false);
    }

    $result = $this->dataSource->query($sqlStatement);

    if ($errorMessage = $result->getErrorMessage()) {
      $this->errorMessage = $errorMessage;

      return(false);
    } else {
      return($result);
    }
  }

  // List the tables of a database
  function listTables() {
    if (! $this->dataSource) {
      return(false);
    }

    $result = $this->dataSource->listTables();

    if ($errorMessage = $result->getErrorMessage()) {
      $this->errorMessage = $errorMessage;

      return(false);
    } else {
      return($result);
    }
  }

}

?>
