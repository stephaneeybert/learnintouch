<?

class ElearningSessionCourseDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_SESSION_COURSE;

    $this->dao = new ElearningSessionCourseDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningSessionCourse();
      $object->setId($row['id']);
      $object->setElearningSessionId($row['elearning_session_id']);
      $object->setElearningCourseId($row['elearning_course_id']);

      return($object);
    }
  }

  function selectById($elearningSessionCourseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($elearningSessionCourseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectBySessionId($elearningSessionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionId($elearningSessionId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);

        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseId($elearningCourseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseId($elearningCourseId)) {
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

    return($this->dao->insert($object->getElearningSessionId(), $object->getElearningCourseId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getElearningSessionId(), $object->getElearningCourseId()));
  }

  function delete($elearningSessionCourseId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningSessionCourseId));
  }

  function deleteBySessionId($elearningSessionId) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteBySessionId($elearningSessionId));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
