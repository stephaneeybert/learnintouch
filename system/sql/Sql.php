<?php

class Sql {

  // Initialize a data source
  static function initDataSource($dbName = '') {

    if (!$dbName) {
      $dbName = DB_NAME;
    }

    $dataSource = new DataSourceMySQL(DB_HOST, $dbName);

    if (!$dataSource->connect(DB_USER, DB_PASS)) {
error_log("initDataSource DB_HOST: " . DB_HOST);
      die("The data source for the database $dbName could not be initialized for the user " . DB_USER . ".");
    }

    return($dataSource);
  }

  // Initialize the common data source
  static function initCommonDataSource($dbName = '') {

    if (!$dbName) {
      $dbName = DB_COMMON_DB_NAME;
    }

    $dataSource = new DataSourceMySQL(DB_COMMON_HOST, $dbName);

    if (!$dataSource->connect(DB_COMMON_USER, DB_COMMON_PASS)) {
error_log("initCommonDataSource DB_COMMON_HOST: " . DB_COMMON_HOST . " DB_COMMON_PASS: " . DB_COMMON_PASS . " DB_COMMON_USER: " . DB_COMMON_USER . " dbName: " . $dbName);
      die("The data source for the database $dbName could not be initialized for the user " . DB_COMMON_USER . ".");
    }

    return($dataSource);
  }

}

?>
