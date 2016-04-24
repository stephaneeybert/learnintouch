<?

class BackupDB {

  var $dataSource;
  var $dao;

  function BackupDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->dao = new BackupDao($this->dataSource);
    }

  // Select the data source to save the common database
  function selectCommonDataSource() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->dao = new BackupDao($this->dataSource);
    }

  }

?>
