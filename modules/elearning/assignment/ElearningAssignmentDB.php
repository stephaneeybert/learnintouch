<?

class ElearningAssignmentDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningAssignmentDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_ASSIGNMENT;

    $this->dao = new ElearningAssignmentDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningAssignment();
      $object->setId($row['id']);
      $object->setElearningSubscriptionId($row['elearning_subscription_id']);
      $object->setElearningExerciseId($row['elearning_exercise_id']);
      $object->setElearningResultId($row['elearning_result_id']);
      $object->setOnlyOnce($row['only_once']);
      $object->setOpeningDate($row['opening_date']);
      $object->setClosingDate($row['closing_date']);
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

  function selectBySubscriptionId($elearningSubscriptionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionId($elearningSubscriptionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByExerciseId($elearningExerciseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExerciseId($elearningExerciseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultId($elearningResultId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultId($elearningResultId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectBySubscriptionIdAndOpened($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionIdAndOpened($elearningSubscriptionId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionIdAndNotClosed($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionIdAndNotClosed($elearningSubscriptionId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionIdAndDeferred($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionIdAndDeferred($elearningSubscriptionId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndResultWithinSessionId($elearningClassId, $elearningSessionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndResultWithinSessionId($elearningClassId, $elearningSessionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultAndSubscriptionId($elearningSubscriptionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultAndSubscriptionId($elearningSubscriptionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultWithinSessionId($elearningSessionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultWithinSessionId($elearningSessionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndResultSinceReleaseDate($elearningClassId, $sinceDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndResultSinceReleaseDate($elearningClassId, $sinceDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultSinceReleaseDate($sinceDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultSinceReleaseDate($sinceDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassId($elearningClassId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassId($elearningClassId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndOpened($elearningClassId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndOpened($elearningClassId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndClosed($elearningClassId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndClosed($elearningClassId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndNotClosed($elearningClassId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndNotClosed($elearningClassId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndDeferred($elearningClassId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndDeferred($elearningClassId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNotClosed($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotClosed($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectOpened($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectOpened($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectDeferred($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectDeferred($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectClosed($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectClosed($systemDate, $start, $rows)) {
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

    return($this->dao->insert($object->getElearningSubscriptionId(), $object->getElearningExerciseId(), $object->getElearningResultId(), $object->getOnlyOnce(), $object->getOpeningDate(), $object->getClosingDate()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getElearningSubscriptionId(), $object->getElearningExerciseId(), $object->getElearningResultId(), $object->getOnlyOnce(), $object->getOpeningDate(), $object->getClosingDate()));
  }

  function delete($elearningSubscriptionId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningSubscriptionId));
  }

  function deleteBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
