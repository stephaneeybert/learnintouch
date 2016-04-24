<?

class StatisticsPageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function StatisticsPageDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_STATISTICS_PAGE;

    $this->dao = new StatisticsPageDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new StatisticsPage();
      $object->setId($row['id']);
      $object->setPage($row['page']);
      $object->setHits($row['hits']);
      $object->setMonth($row['month']);
      $object->setYear($row['year']);

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

  function selectByYearAndMonth($year, $month, $limit = '') {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByYearAndMonth($year, $month, $limit)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByPageAndYearAndMonth($page, $year, $month) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPageAndYearAndMonth($page, $year, $month)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function addHit($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->addHit($object->getId()));
    }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->insert($object->getPage(), $object->getHits(), $object->getMonth(), $object->getYear()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getPage(), $object->getHits(), $object->getMonth(), $object->getYear()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  function deleteOldStat($year) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteOldStat($year));
    }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
    }

  }

?>
