<?

class StatisticsVisitDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function StatisticsVisitDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_STATISTICS_VISIT;

    $this->dao = new StatisticsVisitDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new StatisticsVisit();
      $object->setId($row['id']);
      $object->setVisitDateTime($row['visit_datetime']);
      $object->setVisitorHostAddress($row['visitor_host_address']);
      $object->setVisitorBrowser($row['visitor_browser']);
      $object->setVisitorReferer($row['visitor_referer']);

      return($object);
      }
    }

  function createIndexVisitDateTime() {
    $this->dataSource->selectDatabase();

    return($this->dao->createIndexVisitDateTime());
    }

  function createIndexVisitorHostAddress() {
    $this->dataSource->selectDatabase();

    return($this->dao->createIndexVisitorHostAddress());
    }

  function createIndexVisitorBrowser() {
    $this->dataSource->selectDatabase();

    return($this->dao->createIndexVisitorBrowser());
    }

  function createIndexVisitorReferer() {
    $this->dataSource->selectDatabase();

    return($this->dao->createIndexVisitorReferer());
    }

  function selectHostLastVisit($visitorHostAddress) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectHostLastVisit($visitorHostAddress)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function countByReferer($year, $month) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->countByReferer($year, $month)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $objects[$i] = array($row['count'], $row['visitor_referer']);
        }
      }

    return($objects);
    }

  function countByBrowser() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->countByBrowser()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $objects[$i] = array($row['count'], $row['visitor_browser']);
        }
      }

    return($objects);
    }

  function countVisitors($visitDateTime) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countVisitors($visitDateTime);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countVisits($visitDateTime) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countVisits($visitDateTime);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countMonthVisitors($year, $month) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countMonthVisitors($year, $month);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countMonthVisits($year, $month) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countMonthVisits($year, $month);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countDayVisitors($year, $month, $day) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDayVisitors($year, $month, $day);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countDayVisits($year, $month, $day) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDayVisits($year, $month, $day);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countWeekDayVisits($year, $day) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countWeekDayVisits($year, $day);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function countHourVisits($year, $hour) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countHourVisits($year, $hour);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $visitorHostAddress = $object->getVisitorHostAddress();
    $visitorHostAddress = LibString::databaseEscapeQuotes($visitorHostAddress);
    $visitorBrowser = $object->getVisitorBrowser();
    $visitorBrowser = LibString::databaseEscapeQuotes($visitorBrowser);
    $visitorReferer = $object->getVisitorReferer();
    $visitorReferer = LibString::databaseEscapeQuotes($visitorReferer);

    return($this->dao->insert($object->getVisitDateTime(), $visitorHostAddress, $visitorBrowser, $visitorReferer));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $visitorHostAddress = $object->getVisitorHostAddress();
    $visitorHostAddress = LibString::databaseEscapeQuotes($visitorHostAddress);
    $visitorBrowser = $object->getVisitorBrowser();
    $visitorBrowser = LibString::databaseEscapeQuotes($visitorBrowser);
    $visitorReferer = $object->getVisitorReferer();
    $visitorReferer = LibString::databaseEscapeQuotes($visitorReferer);

    return($this->dao->update($object->getId(), $object->getVisitDateTime(), $visitorHostAddress, $visitorBrowser, $visitorReferer));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  function countOldVisits($year) {
    $this->dataSource->selectDatabase();

    return($this->dao->countOldVisits($year));
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
