<?php

class StatisticsPageDao extends Dao {

  var $tableName;

  function StatisticsPageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
page varchar(255) not null,
hits int unsigned not null,
month int unsigned not null,
year int unsigned not null,
unique (page, month, year),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($page, $hits, $month, $year) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$page', '$hits', '$month', '$year')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $page, $hits, $month, $year) {
    $sqlStatement = "UPDATE $this->tableName SET page = '$page', hits = '$hits', month = '$month', year = '$year' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function addHit($id) {
    $sqlStatement = "UPDATE $this->tableName SET hits = hits + 1 WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteOldStat($year) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE year < '$year'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY hits DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByYearAndMonth($year, $month, $limit = '') {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE year = '$year' AND month = '$month' ORDER BY hits DESC";
    if ($limit) {
      $sqlStatement .= " LIMIT $limit";
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByPageAndYearAndMonth($page, $year, $month) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE page = '$page' AND year = '$year' AND month = '$month' ORDER BY hits DESC";
    return($this->querySelect($sqlStatement));
  }

}

?>
