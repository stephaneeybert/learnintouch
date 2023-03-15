<?

class ElearningSessionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_SESSION;

    $this->dao = new ElearningSessionDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningSession();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setOpenDate($row['opening_date']);
      $object->setCloseDate($row['closing_date']);
      $object->setClosed($row['closed']);

      return($object);
    }
  }

  function selectById($elearningSessionId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($elearningSessionId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByName($name)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAll() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAll()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNotYetOpened($systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotYetOpened($systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectCurrentlyOpened($systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectCurrentlyOpened($systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectClosed($systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectClosed($systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNotClosed($systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotClosed($systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectLikePatternAndNotClosed($searchPattern, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternAndNotClosed($searchPattern, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionWithTeacherId($teacherId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionWithTeacherId($teacherId)) {
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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getOpenDate(), $object->getCloseDate(), $object->getClosed()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getOpenDate(), $object->getCloseDate(), $object->getClosed()));
  }

  function delete($elearningSessionId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningSessionId));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
