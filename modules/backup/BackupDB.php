<?

class BackupDB {

  var $dataSource;
  var $dao;

  function BackupDB() {
    $this->dataSource = Sql::initDataSource();

    $this->dao = new BackupDao($this->dataSource);
    }

  // Select the data source to save the common database
  function selectCommonDataSource() {
    $this->dataSource = Sql::initCommonDataSource();

    $this->dao = new BackupDao($this->dataSource);
    }

  }

?>
