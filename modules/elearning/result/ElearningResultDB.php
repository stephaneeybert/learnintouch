<?

class ElearningResultDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningResultDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_RESULT;

    $this->dao = new ElearningResultDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningResult();
      $object->setId($row['id']);
      $object->setElearningExerciseId($row['elearning_exercise_id']);
      $object->setSubscriptionId($row['subscription_id']);
      $object->setExerciseDate($row['exercise_datetime']);
      $object->setExerciseElapsedTime($row['exercise_elapsed_time']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setMessage($row['message']);
      $object->setComment($row['text_comment']);
      $object->setHideComment($row['hide_comment']);
      $object->setEmail($row['email']);
      $object->setNbReadingQuestions($row['nb_reading_questions']);
      $object->setNbCorrectReadingAnswers($row['nb_correct_reading_answers']);
      $object->setNbIncorrectReadingAnswers($row['nb_incorrect_reading_answers']);
      $object->setNbReadingPoints($row['nb_reading_points']);
      $object->setNbWritingQuestions($row['nb_writing_questions']);
      $object->setNbCorrectWritingAnswers($row['nb_correct_writing_answers']);
      $object->setNbIncorrectWritingAnswers($row['nb_incorrect_writing_answers']);
      $object->setNbWritingPoints($row['nb_writing_points']);
      $object->setNbListeningQuestions($row['nb_listening_questions']);
      $object->setNbCorrectListeningAnswers($row['nb_correct_listening_answers']);
      $object->setNbIncorrectListeningAnswers($row['nb_incorrect_listening_answers']);
      $object->setNbListeningPoints($row['nb_listening_points']);
      $object->setNbNotAnswered($row['nb_not_answered']);
      $object->setNbIncorrectAnswers($row['nb_incorrect_answers']);

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

  function selectOldResults($sinceDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectOldResults($sinceDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNonSubscriptions($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNonSubscriptions($start, $rows)) {
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

  function selectByReleaseDate($sinceDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByReleaseDate($sinceDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByExerciseId($exerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExerciseId($exerciseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionIdAndCourseId($subscriptionId, $courseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionIdAndCourseId($subscriptionId, $courseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubscriptionId($subscriptionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubscriptionId($subscriptionId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByUserId($userId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserId($userId, $start, $rows)) {
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

  function selectBySessionIdAndCourseIdAndClassIdAndExerciseId($sessionId, $courseId, $classId, $exerciseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseIdAndClassIdAndExerciseId($sessionId, $courseId, $classId, $exerciseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByClassIdAndExerciseId($classId, $exerciseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassIdAndExerciseId($classId, $exerciseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseIdAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseIdAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start, $rows)) {
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

  function selectByClassId($classId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByClassId($classId, $start, $rows)) {
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

  function selectByCourseIdAndClassId($courseId, $classId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdAndClassId($courseId, $classId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySessionIdAndCourseIdAndTeacherId($sessionId, $courseId, $teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseIdAndTeacherId($sessionId, $courseId, $teacherId, $start, $rows)) {
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

  function selectBySessionIdAndCourseIdAndExerciseId($sessionId, $courseId, $exerciseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndCourseIdAndExerciseId($sessionId, $courseId, $exerciseId, $start, $rows)) {
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

  function selectByCourseId($courseId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseId($courseId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByTeacherId($teacherId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTeacherId($teacherId, $start, $rows)) {
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

  function selectBySubscriptionAndExercise($subscriptionId, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectBySubscriptionAndExercise($subscriptionId, $elearningExerciseId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByEmail($email) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByEmail($email)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByEmailAndExercise($email, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByEmailAndExercise($email, $elearningExerciseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByEmailAndExerciseAndDate($email, $elearningExerciseId, $exerciseDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByEmailAndExerciseAndDate($email, $elearningExerciseId, $exerciseDate)) {
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

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);
    $comment = $object->getComment();
    $comment = LibString::databaseEscapeQuotes($comment);

    return($this->dao->insert($object->getElearningExerciseId(), $object->getSubscriptionId(), $object->getExerciseDate(), $object->getExerciseElapsedTime(), $object->getFirstname(), $object->getLastname(), $message, $comment, $object->getHideComment(), $object->getEmail(), $object->getNbReadingQuestions(), $object->getNbCorrectReadingAnswers(), $object->getNbIncorrectReadingAnswers(), $object->getNbReadingPoints(), $object->getNbWritingQuestions(), $object->getNbCorrectWritingAnswers(), $object->getNbIncorrectWritingAnswers(), $object->getNbWritingPoints(), $object->getNbListeningQuestions(), $object->getNbCorrectListeningAnswers(), $object->getNbIncorrectListeningAnswers(), $object->getNbListeningPoints(), $object->getNbNotAnswered(), $object->getNbIncorrectAnswers()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);
    $comment = $object->getComment();
    $comment = LibString::databaseEscapeQuotes($comment);

    return($this->dao->update($object->getId(), $object->getElearningExerciseId(), $object->getSubscriptionId(), $object->getExerciseDate(), $object->getExerciseElapsedTime(), $object->getFirstname(), $object->getLastname(), $message, $comment, $object->getHideComment(), $object->getEmail(), $object->getNbReadingQuestions(), $object->getNbCorrectReadingAnswers(), $object->getNbIncorrectReadingAnswers(), $object->getNbReadingPoints(), $object->getNbWritingQuestions(), $object->getNbCorrectWritingAnswers(), $object->getNbIncorrectWritingAnswers(), $object->getNbWritingPoints(), $object->getNbListeningQuestions(), $object->getNbCorrectListeningAnswers(), $object->getNbIncorrectListeningAnswers(), $object->getNbListeningPoints(), $object->getNbNotAnswered(), $object->getNbIncorrectAnswers()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
