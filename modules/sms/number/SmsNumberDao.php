<?php

class SmsNumberDao extends Dao {

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
firstname varchar(255),
lastname varchar(255),
mobile_phone varchar(20) not null,
subscribe boolean not null,
imported boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $mobilePhone, $subscribe, $imported) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$mobilePhone', '$subscribe', '$imported')";
    return($this->querySelect($sqlStatement));
  }

  function resetImported() {
    $sqlStatement = "UPDATE $this->tableName SET imported != '1'";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $mobilePhone, $subscribe, $imported) {
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', mobile_phone = '$mobilePhone', subscribe = '$subscribe', imported = '$imported' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteImported() {
    $sqlStatement = "DELETE FROM $this->tableName WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countImported() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectImported() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE imported = '1' ORDER BY lastname, firstname, mobile_phone";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY lastname, firstname, mobile_phone";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByMobilePhone($mobilePhone) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mobile_phone = '$mobilePhone' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE mobile_phone LIKE '%$searchPattern%' OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%')";
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

  function selectSubscribers($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscribe = '1' ORDER BY lastname, firstname, mobile_phone";
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

  function selectNonSubscribers($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscribe != '1' ORDER BY lastname, firstname, mobile_phone";
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

}

?>
