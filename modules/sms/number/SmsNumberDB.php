<?

class SmsNumberDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function SmsNumberDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_SMS_NUMBER;

    $this->dao = new SmsNumberDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new SmsNumber();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setMobilePhone($row['mobile_phone']);
      $object->setSubscribe($row['subscribe']);
      $object->setImported($row['imported']);

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

  function selectByMobilePhone($mobilePhone) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByMobilePhone($mobilePhone)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function countImported() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countImported();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectImported() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectImported()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
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

  function selectSubscribers($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectSubscribers($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectNonSubscribers($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNonSubscribers($start, $rows)) {
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

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getMobilePhone(), $object->getSubscribe(), $object->getImported()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(), $object->getMobilePhone(), $object->getSubscribe(), $object->getImported()));
    }

  // Reset the imported status of all mobile phone numbers
  function resetImported() {
    $this->dataSource->selectDatabase();

    return($this->dao->resetImported());
    }

  function deleteImported() {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteImported());
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
