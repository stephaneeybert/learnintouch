<?php

class ElearningSessionDao extends Dao {

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
name varchar(100) not null,
unique(name),
description varchar(255),
opening_date datetime not null,
closing_date datetime,
closed boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $openDate, $closeDate, $closed) {
    $closeDate = LibString::emptyToNULL($closeDate);
    $closeDate = LibString::addSingleQuotesIfNotNULL($closeDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$openDate', $closeDate, '$closed')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $openDate, $closeDate, $closed) {
    $closeDate = LibString::emptyToNULL($closeDate);
    $closeDate = LibString::addSingleQuotesIfNotNULL($closeDate);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', opening_date = '$openDate', closing_date = $closeDate, closed = '$closed' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectNotYetOpened($systemDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE closed != '1' AND '$systemDate' < opening_date ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectCurrentlyOpened($systemDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE closed != '1' AND '$systemDate' >= opening_date AND ('$systemDate' <= closing_date OR closing_date IS NULL) ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectClosed($systemDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE closed = '1' OR ('$systemDate' > closing_date AND closing_date IS NOT NULL) ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectNotClosed($systemDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE closed != '1' AND (('$systemDate' <= closing_date AND closing_date IS NOT NULL) OR closing_date IS NULL) ORDER BY opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePatternAndNotClosed($searchPattern, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE ((lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%')) OR id = '$searchPattern') AND closed != '1' AND (('$systemDate' <= closing_date AND closing_date IS NOT NULL) OR closing_date IS NULL) ORDER BY opening_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySubscriptionWithTeacherId($teacherId) {
    $sqlStatement = "SELECT es.* FROM $this->tableName es, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " esu WHERE esu.teacher_id = '$teacherId' AND esu.session_id = es.id ORDER BY es.opening_date DESC";
    return($this->querySelect($sqlStatement));
  }

}

?>
