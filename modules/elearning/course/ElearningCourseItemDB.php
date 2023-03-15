<?

class ElearningCourseItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_COURSE_ITEM;

    $this->dao = new ElearningCourseItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningCourseItem();
      $object->setId($row['id']);
      $object->setElearningCourseId($row['elearning_course_id']);
      $object->setElearningExerciseId($row['elearning_exercise_id']);
      $object->setElearningLessonId($row['elearning_lesson_id']);
      $object->setListOrder($row['list_order']);

      return($object);
    }
  }

  function selectById($elearningCourseItemId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($elearningCourseItemId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByCourseIdAndLessonExerciseId($elearningCourseId, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByCourseIdAndLessonExerciseId($elearningCourseId, $elearningExerciseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByNextListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($elearningCourseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByPreviousListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($elearningCourseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);

        return($object);
      }
    }
  }

  function selectByListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($elearningCourseId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset the list order of all the objects if some mistakenly share the same list order
  function resetListOrder($elearningCourseId) {
    if ($this->countDuplicateListOrderRows($elearningCourseId) > 0) {
      if ($elearningCourses = $this->selectByCourseIdOrderById($elearningCourseId)) {
        if (count($elearningCourses) > 0) {
          $listOrder = 0;
          foreach ($elearningCourses as $elearningCourse) {
            $listOrder = $listOrder + 1;
            $elearningCourse->setListOrder($listOrder);
            $this->update($elearningCourse);
          }
        }
      }
    }
  }

  // Count the number of objects that have the same list order
  // There should be zero if the ordering of the objects is correct
  function countDuplicateListOrderRows($elearningCourseId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningCourseId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByCourseId($elearningCourseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseId($elearningCourseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseIdOrderById($elearningCourseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdOrderById($elearningCourseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByExerciseId($elearningExerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExerciseId($elearningExerciseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLessonId($elearningLessonId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonId($elearningLessonId)) {
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

    return($this->dao->insert($object->getElearningCourseId(), $object->getElearningExerciseId(), $object->getElearningLessonId(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getElearningCourseId(), $object->getElearningExerciseId(), $object->getElearningLessonId(), $object->getListOrder()));
  }

  function delete($elearningCourseItemId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningCourseItemId));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
