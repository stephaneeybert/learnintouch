<?

class SqlToolsDB {

  var $dataSource;
  var $dao;

  function SqlToolsDB($dbName) {
    $this->dataSource = Sql::initDataSource($dbName);

    $this->dao = new SqlToolsDao($this->dataSource);
  }

  // Release the data source
  function freeDataSource() {
    $this->dataSource->disconnect();
  }

  // Get the database size
  function getDatabaseSize() {
    $dbSize = 0;

    $this->dataSource->selectDatabase();

    // If the sql statement is successful and a result is returned
    if ($result = $this->dao->getDatabaseSize()) {
      // Then get the rows
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);

        $dataLength = $row['Data_length'];
        $indexLength = $row['Index_length'];

        $dbSize += $dataLength + $indexLength;
      }
    }

    // Return the size in mega bytes
    $dbSize = ceil($dbSize / (1024 * 1024));

    return($dbSize);
  }

  // Perform an sql statement
  function performStatement($sqlStatement) {
    $this->dataSource->selectDatabase();

    return($this->dao->performStatement($sqlStatement));
  }

  // Get all database names
  function getDatabaseNames() {
    $this->dataSource->selectDatabase();

    $names = Array();
    /* TODO Not used
    if ($result = $this->dao->getDatabaseNames()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);

        $name = $row['Database'];
        // DB_COMMON_DB_NAME is the name of the database common to and used by
        // all the web sites, and DB_SYSTEM_DB_NAME is the name of the database used
        // only by the RDBMS to manage user and table rights (mysql in MySql)
        if (strstr($name, DB_NAME_PREFIX) && $name != DB_COMMON_DB_NAME && $name != DB_SYSTEM_DB_NAME) {
          array_push($names, $name);
        }
      }
    }
    */
    array_push($names, "europasprak");

    return($names);
  }

  // Get the possible error message
  function getErrorMessage() {
    return($this->dao->errorMessage);
  }

}

?>
