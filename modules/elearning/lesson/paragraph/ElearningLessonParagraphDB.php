<?

class ElearningLessonParagraphDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_LESSON_PARAGRAPH;

    $this->dao = new ElearningLessonParagraphDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningLessonParagraph();
      $object->setId($row['id']);
      $object->setHeadline($row['headline']);
      $object->setBody($row['body']);
      $object->setImage($row['image']);
      $object->setAudio($row['audio']);
      $object->setVideo($row['video']);
      $object->setVideoUrl($row['video_url']);
      $object->setListOrder($row['list_order']);
      $object->setElearningLessonId($row['elearning_lesson_id']);
      $object->setElearningLessonHeadingId($row['elearning_lesson_heading_id']);
      $object->setElearningExerciseId($row['elearning_exercise_id']);
      $object->setExerciseTitle($row['exercise_title']);

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

  function selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($elearningLessonId, $elearningLessonHeadingId) {
    if ($this->countDuplicateListOrderRows($elearningLessonId, $elearningLessonHeadingId) > 0) {
      if ($newsStories = $this->selectByLessonIdAndLessonHeadingIdOrderById($elearningLessonId, $elearningLessonHeadingId)) {
        if (count($newsStories) > 0) {
          $listOrder = 0;
          foreach ($newsStories as $newsStory) {
            $listOrder = $listOrder + 1;
            $newsStory->setListOrder($listOrder);
            $this->update($newsStory);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($elearningLessonId, $elearningLessonHeadingId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningLessonId, $elearningLessonHeadingId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
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

  function selectByOtherLessonExerciseId($elearningExerciseId, $elearningLessonId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByOtherLessonExerciseId($elearningExerciseId, $elearningLessonId)) {
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

  function selectByLessonIdAndExerciseId($elearningLessonId, $elearningExerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonIdAndExerciseId($elearningLessonId, $elearningExerciseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLessonIdAndLessonHeadingIdOrderById($elearningLessonId, $elearningLessonHeadingId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonIdAndLessonHeadingIdOrderById($elearningLessonId, $elearningLessonHeadingId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLessonHeadingId($elearningLessonHeadingId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonHeadingId($elearningLessonHeadingId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLessonIdAndNoLessonHeading($elearningLessonId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByLessonIdAndNoLessonHeading($elearningLessonId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectWithInvalidModelHeading($elearningLessonId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectWithInvalidModelHeading($elearningLessonId)) {
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

    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);

    return($this->dao->insert($object->getHeadline(), $body, $object->getImage(), $object->getAudio(), $object->getVideo(), $object->getVideoUrl(), $object->getListOrder(), $object->getElearningLessonId(), $object->getElearningLessonHeadingId(), $object->getElearningExerciseId(), $object->getExerciseTitle()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);

    return($this->dao->update($object->getId(), $object->getHeadline(), $body, $object->getImage(), $object->getAudio(), $object->getVideo(), $object->getVideoUrl(), $object->getListOrder(), $object->getElearningLessonId(), $object->getElearningLessonHeadingId(), $object->getElearningExerciseId(), $object->getExerciseTitle()));
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
