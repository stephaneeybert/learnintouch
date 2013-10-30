<?

class ElearningSolutionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningSolutionDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_SOLUTION;

    $this->dao = new ElearningSolutionDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningSolution();
      $object->setId($row['id']);
      $object->setElearningQuestion($row['elearning_question_id']);
      $object->setElearningAnswer($row['elearning_answer_id']);

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

  function selectByQuestionAndAnswer($elearningQuestion, $elearningAnswer) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByQuestionAndAnswer($elearningQuestion, $elearningAnswer)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByAnswer($elearningAnswerId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByAnswer($elearningAnswerId)) {
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

    return($this->dao->insert($object->getElearningQuestion(), $object->getElearningAnswer()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getElearningQuestion(), $object->getElearningAnswer()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
