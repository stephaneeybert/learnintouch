<?

class ElearningQuestionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_QUESTION;

    $this->dao = new ElearningQuestionDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningQuestion();
      $object->setId($row['id']);
      $object->setQuestion($row['question']);
      $object->setExplanation($row['explanation']);
      $object->setElearningExercisePage($row['elearning_exercise_page_id']);
      $object->setImage($row['image']);
      $object->setAudio($row['audio']);
      $object->setHint($row['hint']);
      $object->setPoints($row['points']);
      $object->setAnswerNbWords($row['answer_nb_words']);
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

  function selectByNextListOrder($elearningExercisePageId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($elearningExercisePageId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($elearningExercisePageId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($elearningExercisePageId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($elearningExercisePageId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($elearningExercisePageId, $listOrder)) {
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

  function selectByExercisePageOrderById($elearningExercisePageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExercisePageOrderById($elearningExercisePageId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByExercisePage($elearningExercisePageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExercisePage($elearningExercisePageId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByExercise($elearningExerciseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByExercise($elearningExerciseId)) {
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

    $question = $object->getQuestion();
    $question = LibString::databaseEscapeQuotes($question);

    $hint = $object->getHint();
    $hint = LibString::databaseEscapeQuotes($hint);

    $explanation = $object->getExplanation();
    $explanation = LibString::databaseEscapeQuotes($explanation);

    return($this->dao->insert($question, $explanation, $object->getElearningExercisePage(), $object->getImage(), $object->getAudio(), $hint, $object->getPoints(), $object->getAnswerNbWords(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $question = $object->getQuestion();
    $question = LibString::databaseEscapeQuotes($question);

    $hint = $object->getHint();
    $hint = LibString::databaseEscapeQuotes($hint);

    $explanation = $object->getExplanation();
    $explanation = LibString::databaseEscapeQuotes($explanation);

    return($this->dao->update($object->getId(), $question, $explanation, $object->getElearningExercisePage(), $object->getImage(), $object->getAudio(), $hint, $object->getPoints(), $object->getAnswerNbWords(), $object->getListOrder()));
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
