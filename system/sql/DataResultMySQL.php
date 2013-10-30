<?php

// The functions managing the MySQL specific data result.
// A result can contain one or more rows.
// It can also contain none if an error occured.
// In that case, an error message is retrieved from the database system.

class DataResultMySQL extends DataResult {

  function DataResultMySQL($dataSource, $resultId) {
    // Instanciate the result
    $this->DataResult($dataSource, $resultId);

    // Reset the current row
    $this->currentRow = 0;

    // Set the error message if any
    // Check for the value AND the type (!==) of the result
    if ($resultId !== false) {
      $this->errorMessage = '';
    } else {
      $this->errorMessage = $dataSource->getErrorMessage();
    }
  }

  // Free the data result
  function free() {
    mysql_free_result($this->getResultId());
  }

  // Return the error message if any
  function getErrorMessage() {
    return($this->errorMessage);
  }

  // Check if there was an error
  function noError() {
    if ($this->errorMessage) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the result current row
  function getRow($index, $type = DB_RESULT_ARRAY_BOTH) {
    if ($index != $this->currentRow) {
      mysql_data_seek($this->getResultId(), $index);
    }
    $this->currentRow = $index + 1;

    switch ($type) {
      case DB_RESULT_ARRAY_ASSOC:
        return(mysql_fetch_assoc($this->getResultId()));
      case DB_RESULT_ARRAY_NUM:
        return(mysql_fetch_row($this->getResultId()));
      case DB_RESULT_ARRAY_BOTH:
        return(mysql_fetch_array($this->getResultId()));
      default:
        return(mysql_fetch_array($this->getResultId()));
    }
  }

  // Get the number of rows for the result
  function getRowCount() {
    return(mysql_num_rows($this->getResultId()));
  }

  // Get the number of fields for the result
  function getFieldCount() {
    return(mysql_num_fields($this->getResultId()));
  }

  // Get the name of a field
  function getFieldName($index) {
    return(mysql_field_name($this->getResultId(), $index));
  }

}

?>
