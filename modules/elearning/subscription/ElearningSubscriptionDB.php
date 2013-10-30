<?

class ElearningSubscriptionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningSubscriptionDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_SUBSCRIPTION;

    $this->dao = new ElearningSubscriptionDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningSubscription();
      $object->setId($row['id']);
      $object->setUserId($row['user_account_id']);
      $object->setTeacherId($row['teacher_id']);
      $object->setSessionId($row['session_id']);
      $object->setCourseId($row['course_id']);
      $object->setClassId($row['class_id']);
      $object->setSubscriptionDate($row['subscription_date']);
      $object->setSubscriptionClose($row['subscription_close']);
      $object->setWatchLive($row['watch_live']);
      $object->setLastExerciseId($row['last_exercise_id']);
      $object->setLastExercisePageId($row['last_exercise_page_id']);
      $object->setLastActive($row['last_active']);
      $object->setWhiteboard($row['whiteboard']);
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

  function selectUserSubscriptions($userId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectUserSubscriptions($userId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectOpenedUserSubscriptionsWithCourse($userId, $systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectOpenedUserSubscriptionsWithCourse($userId, $systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectOpenedUserSubscriptions($userId, $systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectOpenedUserSubscriptions($userId, $systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByUserId($userId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserId($userId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByUserIdAndTeacherId($userId, $teacherId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserIdAndTeacherId($userId, $teacherId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByUserIdAndSubscriptionId($userId, $elearningSubscriptionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserIdAndSubscriptionId($userId, $elearningSubscriptionId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }

    return($objects);
  }

  function selectByUserIdAndCourseIdAndSessionId($userId, $courseId, $sessionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserIdAndCourseIdAndSessionId($userId, $courseId, $sessionId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByUserIdAndCourseId($userId, $courseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserIdAndCourseId($userId, $courseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseId($courseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseId($courseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassId($classId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassId($classId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByTeacherId($teacherId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTeacherId($teacherId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
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

  function selectLikePatternDistinctUsers($searchPattern) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternDistinctUsers($searchPattern)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionId($sessionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionId($sessionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNoSessionId($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNoSessionId($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseId($sessionId, $courseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseId($sessionId, $courseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseIdAndClassId($sessionId, $courseId, $classId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseIdAndClassId($sessionId, $courseId, $classId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndTeacherId($sessionId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndTeacherId($sessionId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseAndTeacherId($sessionId, $courseId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseAndTeacherId($sessionId, $courseId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseIdAndTeacherId($courseId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdAndTeacherId($courseId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndTeacherId($classId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndTeacherId($classId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseIdAndClassIdAndTeacherId($courseId, $classId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdAndClassIdAndTeacherId($courseId, $classId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndClassIdAndTeacherId($sessionId, $classId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndClassIdAndTeacherId($sessionId, $classId, $teacherId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndClassId($sessionId, $classId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndClassId($sessionId, $classId, $start, $rows)) {
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

    $whiteboard = $object->getWhiteboard();
    $whiteboard = LibString::databaseEscapeQuotes($whiteboard);

    return($this->dao->insert($object->getUserId(), $object->getTeacherId(), $object->getSessionId(), $object->getCourseId(), $object->getClassId(), $object->getSubscriptionDate(), $object->getSubscriptionClose(), $object->getWatchLive(), $object->getLastExerciseId(), $object->getLastExercisePageId(), $object->getLastActive(), $whiteboard));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $whiteboard = $object->getWhiteboard();
    $whiteboard = LibString::databaseEscapeQuotes($whiteboard);

    return($this->dao->update($object->getId(), $object->getUserId(), $object->getTeacherId(), $object->getSessionId(), $object->getCourseId(), $object->getClassId(), $object->getSubscriptionDate(), $object->getSubscriptionClose(), $object->getWatchLive(), $object->getLastExerciseId(), $object->getLastExercisePageId(), $object->getLastActive(), $whiteboard));
  }

  function delete($elearningSubscriptionId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningSubscriptionId));
  }

  function countOpenedSubscriptions($systemDate) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countOpenedSubscriptions($systemDate);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
