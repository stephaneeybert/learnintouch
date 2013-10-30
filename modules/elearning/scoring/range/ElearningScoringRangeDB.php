<?

class ElearningScoringRangeDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningScoringRangeDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_SCORING_RANGE;

    $this->dao = new ElearningScoringRangeDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningScoringRange();
      $object->setId($row['id']);
      $object->setUpperRange($row['upper_range']);
      $object->setScore($row['score']);
      $object->setAdvice($row['advice']);
      $object->setProposal($row['proposal']);
      $object->setLinkText($row['link_text']);
      $object->setLinkUrl($row['link_url']);
      $object->setScoringId($row['elearning_scoring_id']);

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

  function selectByScoringId($scoringId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByScoringId($scoringId)) {
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

    $score = $object->getScore();
    $score = LibString::databaseEscapeQuotes($score);

    $advice = $object->getAdvice();
    $advice = LibString::databaseEscapeQuotes($advice);

    $proposal = $object->getProposal();
    $proposal = LibString::databaseEscapeQuotes($proposal);

    return($this->dao->insert($object->getUpperRange(), $score, $advice, $proposal, $object->getLinkText(), $object->getLinkUrl(), $object->getScoringId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $score = $object->getScore();
    $score = LibString::databaseEscapeQuotes($score);

    $advice = $object->getAdvice();
    $advice = LibString::databaseEscapeQuotes($advice);

    $proposal = $object->getProposal();
    $proposal = LibString::databaseEscapeQuotes($proposal);

    return($this->dao->update($object->getId(), $object->getUpperRange(), $score, $advice, $proposal, $object->getLinkText(), $object->getLinkUrl(), $object->getScoringId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function deleteByScoringId($scoringId) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteByScoringId($scoringId));
  }

}

?>
