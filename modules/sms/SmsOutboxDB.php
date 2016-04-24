<?

class SmsOutboxDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function SmsOutboxDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_SMS_OUTBOX;

    $this->dao = new SmsOutboxDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new SmsOutbox();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setMobilePhone($row['mobile_phone']);
      $object->setEmail($row['email']);
      $object->setPassword($row['password']);
      $object->setSent($row['sent']);

      return($object);
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

  function selectSent() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectSent()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);

        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectUnsent() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectUnsent()) {
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

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getMobilePhone(), $object->getEmail(), $object->getPassword(), $object->getSent()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(),
    $object->getMobilePhone(), $object->getEmail(), $object->getPassword(), $object->getSent()));
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
