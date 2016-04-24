<?

class ElearningLessonDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningLessonDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_LESSON;

    $this->dao = new ElearningLessonDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningLesson();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setInstructions($row['instructions']);
      $object->setImage($row['image']);
      $object->setAudio($row['audio']);
      $object->setIntroduction($row['introduction']);
      $object->setSecured($row['secured']);
      $object->setPublicAccess($row['public_access']);
      $object->setReleaseDate($row['release_date']);
      $object->setGarbage($row['garbage']);
      $object->setLocked($row['locked']);
      $object->setLessonModelId($row['lesson_model_id']);
      $object->setCategoryId($row['category_id']);
      $object->setLevelId($row['level_id']);
      $object->setSubjectId($row['subject_id']);

      return($object);
    }
  }

  function selectById($elearningLessonId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($elearningLessonId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
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

  function selectByLessonModelId($elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonModelId($elearningLessonModelId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow(0);
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

  function selectLikePatternInLessonAndCourse($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternInLessonAndCourse($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByReleaseDate($sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNonGarbage($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNonGarbage($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectGarbage() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectGarbage()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseIdAndReleaseDate($elearningCourseId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdAndReleaseDate($elearningCourseId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
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

  function selectByCategoryIdAndReleaseDate($categoryId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndReleaseDate($categoryId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryId($categoryId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLevelIdAndReleaseDate($levelId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLevelIdAndReleaseDate($levelId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLevelId($levelId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLevelId($levelId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubjectIdAndReleaseDate($subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubjectIdAndReleaseDate($subjectId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectBySubjectId($subjectId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySubjectId($subjectId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndLevelIdAndReleaseDate($categoryId, $levelId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndLevelIdAndReleaseDate($categoryId, $levelId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndLevelId($categoryId, $levelId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndLevelId($categoryId, $levelId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndSubjectIdAndReleaseDate($categoryId, $subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndSubjectIdAndReleaseDate($categoryId, $subjectId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndSubjectId($categoryId, $subjectId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndSubjectId($categoryId, $subjectId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLevelIdAndSubjectIdAndReleaseDate($levelId, $subjectId, $systemDate, $listIndex, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLevelIdAndSubjectIdAndReleaseDate($levelId, $subjectId, $systemDate, $listIndex, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLevelIdAndSubjectId($levelId, $subjectId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLevelIdAndSubjectId($levelId, $subjectId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndLevelIdAndSubjectIdAndReleaseDate($categoryId, $levelId, $subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndLevelIdAndSubjectIdAndReleaseDate($categoryId, $levelId, $subjectId, $sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdAndLevelIdAndSubjectId($categoryId, $levelId, $subjectId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdAndLevelIdAndSubjectId($categoryId, $levelId, $subjectId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectPublicAccessAndReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectPublicAccessAndReleaseDate($sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectPublicAccess($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectPublicAccess($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectProtectedAndReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectProtectedAndReleaseDate($sinceDate, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectProtected($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectProtected($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function delete($elearningLessonId) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($elearningLessonId));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $introduction = $object->getIntroduction();
    $introduction = LibString::databaseEscapeQuotes($introduction);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    return($this->dao->insert($object->getName(), $object->getDescription(), $instructions, $object->getImage(), $object->getAudio(), $introduction, $object->getSecured(), $object->getPublicAccess(), $object->getReleaseDate(), $object->getGarbage(), $object->getLocked(), $object->getLessonModelId(), $object->getCategoryId(), $object->getLevelId(), $object->getSubjectId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $introduction = $object->getIntroduction();
    $introduction = LibString::databaseEscapeQuotes($introduction);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $instructions, $object->getImage(), $object->getAudio(), $introduction, $object->getSecured(), $object->getPublicAccess(), $object->getReleaseDate(), $object->getGarbage(), $object->getLocked(), $object->getLessonModelId(), $object->getCategoryId(), $object->getLevelId(), $object->getSubjectId()));
  }

}

?>
