<?

class ElearningCourseDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningCourseDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_COURSE;

    $this->dao = new ElearningCourseDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningCourse();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setImage($row['image']);
      $object->setInstantCorrection($row['instant_correction']);
      $object->setInstantCongratulation($row['instant_congratulation']);
      $object->setInstantSolution($row['instant_solution']);
      $object->setImportable($row['importable']);
      $object->setLocked($row['locked']);
      $object->setSecured($row['secured']);
      $object->setFreeSamples($row['free_samples']);
      $object->setAutoSubscription($row['auto_subscription']);
      $object->setAutoUnsubscription($row['auto_unsubscription']);
      $object->setInterruptTimedOutExercise($row['interrupt_timed_out_exercise']);
      $object->setResetExerciseAnswers($row['reset_exercise_answers']);
      $object->setExerciseOnlyOnce($row['exercise_only_once']);
      $object->setExerciseAnyOrder($row['exercise_any_order']);
      $object->setSaveResultOption($row['save_result_option']);
      $object->setShuffleQuestions($row['shuffle_questions']);
      $object->setShuffleAnswers($row['shuffle_answers']);
      $object->setMatterId($row['matter_id']);
      $object->setUserId($row['user_account_id']);

      return($object);
    }
  }

  function selectById($elearningCourseId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($elearningCourseId)) {
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

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByName($name)) {
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

  function selectBySessionIdAndAutoSubscription($elearningSessionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySessionIdAndAutoSubscription($elearningSessionId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectImportable() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectImportable()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAutoSubscription() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAutoSubscription()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByMatterId($matterId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByMatterId($matterId, $start, $rows)) {
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

  function selectLikePatternAndSessionId($searchPattern, $elearningSessionId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternAndSessionId($searchPattern, $elearningSessionId, $start, $rows)) {
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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getImage(), $object->getInstantCorrection(), $object->getInstantCongratulation(), $object->getInstantSolution(), $object->getImportable(), $object->getLocked(), $object->getSecured(), $object->getFreeSamples(), $object->getAutoSubscription(), $object->getAutoUnsubscription(), $object->getInterruptTimedOutExercise(), $object->getResetExerciseAnswers(), $object->getExerciseOnlyOnce(), $object->getExerciseAnyOrder(), $object->getSaveResultOption(), $object->getShuffleQuestions(), $object->getShuffleAnswers(), $object->getMatterId(), $object->getUserId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getImage(), $object->getInstantCorrection(), $object->getInstantCongratulation(), $object->getInstantSolution(), $object->getImportable(), $object->getLocked(), $object->getSecured(), $object->getFreeSamples(), $object->getAutoSubscription(), $object->getAutoUnsubscription(), $object->getInterruptTimedOutExercise(), $object->getResetExerciseAnswers(), $object->getExerciseOnlyOnce(), $object->getExerciseAnyOrder(), $object->getSaveResultOption(), $object->getShuffleQuestions(), $object->getShuffleAnswers(), $object->getMatterId(), $object->getUserId()));
  }

  function delete($elearningCourseId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningCourseId));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
