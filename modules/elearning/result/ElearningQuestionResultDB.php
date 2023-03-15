<?

class ElearningQuestionResultDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_QUESTION_RESULT;

    $this->dao = new ElearningQuestionResultDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningQuestionResult();
      $object->setId($row['id']);
      $object->setElearningResult($row['elearning_result_id']);
      $object->setElearningQuestion($row['elearning_question_id']);
      $object->setElearningAnswerId($row['elearning_answer_id']);
      $object->setElearningAnswerText($row['elearning_answer_text']);
      $object->setElearningAnswerOrder($row['elearning_answer_order']);

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

  function selectByResult($elearningResultId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResult($elearningResultId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultAndQuestion($elearningResultId, $elearningQuestionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByQuestionId($elearningQuestionId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByQuestionId($elearningQuestionId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByAnswerId($elearningAnswerId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAnswerId($elearningAnswerId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByQuestionAndAnswerId($elearningQuestionId, $elearningAnswerId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByQuestionAndAnswerId($elearningQuestionId, $elearningAnswerId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultAndExercisePageId($elearningResultId, $elearningExercisePageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByResultAndExercisePageId($elearningResultId, $elearningExercisePageId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $elearningAnswerId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $elearningAnswerId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $elearningAnswerText = $object->getElearningAnswerText();
    $elearningAnswerText = LibString::databaseEscapeQuotes($elearningAnswerText);

    return($this->dao->insert($object->getElearningResult(), $object->getElearningQuestion(), $object->getElearningAnswerId(), $elearningAnswerText, $object->getElearningAnswerOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $elearningAnswerText = $object->getElearningAnswerText();
    $elearningAnswerText = LibString::databaseEscapeQuotes($elearningAnswerText);

    return($this->dao->update($object->getId(), $object->getElearningResult(), $object->getElearningQuestion(), $object->getElearningAnswerId(), $elearningAnswerText, $object->getElearningAnswerOrder()));
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
