<?

class MailOutboxDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function MailOutboxDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_MAIL_OUTBOX;

    $this->dao = new MailOutboxDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new MailOutbox();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setEmail($row['email']);
      $object->setPassword($row['password']);
      $object->setSent($row['sent']);
      $object->setErrorMessage($row['error_message']);
      $object->setMetaNames($row['meta_names']);

      return($object);
    }
  }

  function selectById($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function countAll() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countAll();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function countUnsent() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countUnsent();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function countFoundRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countFoundRows();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function countFailed() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countFailed();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectAll($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAll($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectLikePattern($searchPattern) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectSent($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectSent($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectUnsent($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectUnsent($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $metaNames = $object->getMetaNames();
    $metaNames = LibString::databaseEscapeQuotes($metaNames);

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getEmail(), $object->getPassword(), $object->getSent(), $object->getErrorMessage(), $metaNames));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $metaNames = $object->getMetaNames();
    $metaNames = LibString::databaseEscapeQuotes($metaNames);
    $firstname = $object->getFirstname();
    $firstname = LibString::databaseEscapeQuotes($firstname);
    $lastname = $object->getLastname();
    $lastname = LibString::databaseEscapeQuotes($lastname);

    return($this->dao->update($object->getId(), $firstname, $lastname, $object->getEmail(), $object->getPassword(), $object->getSent(), $object->getErrorMessage(), $metaNames));
  }

  function deleteAll() {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteAll());
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  // Get the id of the last inserted object
  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
