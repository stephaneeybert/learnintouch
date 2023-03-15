<?php

class StatisticsVisitDao extends Dao {

  var $tableName;

  function __construct($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
visit_datetime datetime not null,
visitor_host_address varchar(255) not null,
visitor_browser varchar(255) not null,
visitor_referer varchar(255) not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  // Create an index to speed up the search
  function createIndexVisitDateTime() {
    $sqlStatement = "alter table " . $this->tableName . " add index visit_datetime (visit_datetime)";

    return($this->querySelect($sqlStatement));
  }

  // Create an index to speed up the search
  function createIndexVisitorHostAddress() {
    $sqlStatement = "alter table " . $this->tableName . " add index visitor_host_address (visitor_host_address)";

    return($this->querySelect($sqlStatement));
  }

  // Create an index to speed up the search
  function createIndexVisitorBrowser() {
    $sqlStatement = "alter table " . $this->tableName . " add index visitor_browser (visitor_browser)";

    return($this->querySelect($sqlStatement));
  }

  // Create an index to speed up the search
  function createIndexVisitorReferer() {
    $sqlStatement = "alter table " . $this->tableName . " add index visitor_referer (visitor_referer)";

    return($this->querySelect($sqlStatement));
  }

  function insert($visitDateTime, $visitorHostAddress, $visitorBrowser, $visitorReferer) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$visitDateTime', '$visitorHostAddress', '$visitorBrowser', '$visitorReferer')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $visitDateTime, $visitorHostAddress, $visitorBrowser, $visitorReferer) {
    $sqlStatement = "UPDATE $this->tableName SET visit_datetime = '$visitDateTime', visitor_host_address = '$visitorHostAddress', visitor_browser = '$visitorBrowser', visitor_referer = '$visitorReferer' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countOldVisits($year) {
    $sqlStatement = "SELECT count(*) FROM $this->tableName WHERE YEAR(visit_datetime) < '$year'";
    return($this->querySelect($sqlStatement));
  }

  function deleteOldStat($year) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE YEAR(visit_datetime) < '$year'";
    return($this->querySelect($sqlStatement));
  }

  function countByReferer($year, $month) {
    $sqlStatement = "SELECT COUNT(*) as count, visitor_referer FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND MONTH(visit_datetime) = '$month' GROUP BY visitor_referer";
    return($this->querySelect($sqlStatement));
  }

  function countByBrowser() {
    $sqlStatement = "SELECT COUNT(*) as count, visitor_browser FROM $this->tableName GROUP BY visitor_browser";
    return($this->querySelect($sqlStatement));
  }

  function selectHostLastVisit($visitorHostAddress) {
    $sqlStatement = "SELECT id, MAX(visit_datetime) as visit_datetime, visitor_host_address, visitor_browser, visitor_referer FROM $this->tableName WHERE visitor_host_address = '$visitorHostAddress' GROUP BY visitor_host_address";
    return($this->querySelect($sqlStatement));
  }

  function countMonthVisitors($year, $month) {
    $sqlStatement = "SELECT COUNT(DISTINCT visitor_host_address) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND MONTH(visit_datetime) = '$month'";
    return($this->querySelect($sqlStatement));
  }

  function countMonthVisits($year, $month) {
    $sqlStatement = "SELECT COUNT(*) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND MONTH(visit_datetime) = '$month'";
    return($this->querySelect($sqlStatement));
  }

  function countDayVisitors($year, $month, $day) {
    $sqlStatement = "SELECT COUNT(DISTINCT visitor_host_address) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND MONTH(visit_datetime) = '$month' AND DAYOFMONTH(visit_datetime) = '$day'";
    return($this->querySelect($sqlStatement));
  }

  function countDayVisits($year, $month, $day) {
    $sqlStatement = "SELECT COUNT(*) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND MONTH(visit_datetime) = '$month' AND DAYOFMONTH(visit_datetime) = '$day'";
    return($this->querySelect($sqlStatement));
  }

  function countWeekDayVisits($year, $day) {
    // MySQL is following the ODBC standard
    // which takes Sunday as the first day of the week
    $day++;
    if ($day > 7) {
      $day = 1;
    }

    $sqlStatement = "SELECT COUNT(*) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND DAYOFWEEK(visit_datetime) = '$day'";
    return($this->querySelect($sqlStatement));
  }

  function countHourVisits($year, $hour) {
    $sqlStatement = "SELECT COUNT(*) as count FROM $this->tableName WHERE YEAR(visit_datetime) = '$year' AND HOUR(visit_datetime) = '$hour'";
    return($this->querySelect($sqlStatement));
  }

  function countVisitors($visitDateTime) {
    $sqlStatement = "SELECT COUNT(DISTINCT visitor_host_address) as count FROM $this->tableName WHERE visit_datetime >= '$visitDateTime'";
    return($this->querySelect($sqlStatement));
  }

  function countVisits($visitDateTime) {
    $sqlStatement = "SELECT COUNT(*) as count FROM $this->tableName WHERE visit_datetime >= '$visitDateTime'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
