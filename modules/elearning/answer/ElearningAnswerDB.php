<?

class ElearningAnswerDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_ANSWER;

    $this->dao = new ElearningAnswerDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningAnswer();
      $object->setId($row['id']);
      $object->setAnswer($row['answer']);
      $object->setExplanation($row['explanation']);
      $object->setImage($row['image']);
      $object->setAudio($row['audio']);
      $object->setElearningQuestion($row['elearning_question_id']);
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

  function selectByNextListOrder($elearningAnswerId, $elearningQuestion, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($elearningAnswerId, $elearningQuestion, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($elearningAnswerId, $elearningQuestion, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($elearningAnswerId, $elearningQuestion, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  // Reset the list order of all the objects if some mistakenly share the same list order
  function resetListOrder($elearningQuestionId) {
    if ($elearningQuestionId && $this->countDuplicateListOrderRows($elearningQuestionId) > 0) {
      if ($elearningAnswers = $this->selectByQuestionOrderById($elearningQuestionId)) {
        if (count($elearningAnswers) > 0) {
          $listOrder = 0;
          foreach ($elearningAnswers as $elearningAnswer) {
            $listOrder = $listOrder + 1;
            $elearningAnswer->setListOrder($listOrder);
            $this->update($elearningAnswer);
          }
        }
      }
    }
  }

  // Count the number of objects that have the same list order
  // There should be zero if the ordering of the objects is correct
  function countDuplicateListOrderRows($elearningQuestionId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningQuestionId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByListOrder($elearningQuestion, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByListOrder($elearningQuestion, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByQuestionAndAnswer($elearningQuestion, $answer) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByQuestionAndAnswer($elearningQuestion, $answer)) {
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

  function selectByQuestion($elearningQuestion) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByQuestion($elearningQuestion)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByQuestionOrderById($elearningQuestion) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByQuestionOrderById($elearningQuestion)) {
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

    $answer = $object->getAnswer();
    $answer = LibString::databaseEscapeQuotes($answer);

    $explanation = $object->getExplanation();
    $explanation = LibString::databaseEscapeQuotes($explanation);

    return($this->dao->insert($answer, $explanation, $object->getImage(), $object->getAudio(), $object->getElearningQuestion(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $answer = $object->getAnswer();
    $answer = LibString::databaseEscapeQuotes($answer);

    $explanation = $object->getExplanation();
    $explanation = LibString::databaseEscapeQuotes($explanation);

    return($this->dao->update($object->getId(), $answer, $explanation, $object->getImage(), $object->getAudio(), $object->getElearningQuestion(), $object->getListOrder()));
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
