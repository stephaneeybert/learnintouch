<?

class ElearningExercisePageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_EXERCISE_PAGE;

    $this->dao = new ElearningExercisePageDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningExercisePage();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setInstructions($row['instructions']);
      $object->setText($row['text']);
      $object->setHideText($row['hide_text']);
      $object->setTextMaxHeight($row['text_max_height']);
      $object->setImage($row['image']);
      $object->setAudio($row['audio']);
      $object->setAutostart($row['autostart']);
      $object->setVideo($row['video']);
      $object->setVideoUrl($row['video_url']);
      $object->setQuestionType($row['question_type']);
      $object->setHintPlacement($row['hint_placement']);
      $object->setElearningExerciseId($row['elearning_exercise_id']);
      $object->setListOrder($row['list_order']);

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

  function selectByNextListOrder($elearningExerciseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($elearningExerciseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($elearningExerciseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($elearningExerciseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($elearningExerciseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByListOrder($elearningExerciseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  // Reset the list order of all the objects if some mistakenly share the same list order
  function resetListOrder($elearningExerciseId) {
    if ($elearningExerciseId && $this->countDuplicateListOrderRows($elearningExerciseId) > 0) {
      if ($elearningExercisePages = $this->selectByExerciseIdOrderById($elearningExerciseId)) {
        if (count($elearningExercisePages) > 0) {
          $listOrder = 0;
          foreach ($elearningExercisePages as $elearningExercisePage) {
            $listOrder = $listOrder + 1;
            $elearningExercisePage->setListOrder($listOrder);
            $this->update($elearningExercisePage);
          }
        }
      }
    }
  }

  // Count the number of objects that have the same list order
  // There should be zero if the ordering of the objects is correct
  function countDuplicateListOrderRows($elearningExerciseId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningExerciseId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
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

  function selectByExerciseIdOrderById($elearningExerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExerciseIdOrderById($elearningExerciseId)) {
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    return($this->dao->insert($object->getName(), $object->getDescription(), $instructions, $text, $object->getHideText(), $object->getTextMaxHeight(), $object->getImage(), $object->getAudio(), $object->getAutostart(), $object->getVideo(), $object->getVideoUrl(), $object->getQuestionType(), $object->getHintPlacement(), $object->getElearningExerciseId(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $instructions, $text, $object->getHideText(), $object->getTextMaxHeight(), $object->getImage(), $object->getAudio(), $object->getAutostart(), $object->getVideo(), $object->getVideoUrl(), $object->getQuestionType(), $object->getHintPlacement(), $object->getElearningExerciseId(), $object->getListOrder()));
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
