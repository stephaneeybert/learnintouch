<?php

class ElearningTeacherDao extends Dao {

  var $tableName;

  function ElearningTeacherDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
user_account_id int unsigned not null,
index (user_account_id), foreign key (user_account_id) references user(id),
unique (user_account_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($userId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$userId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $userId) {
    $sqlStatement = "UPDATE $this->tableName SET user_account_id = '$userId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(u.email) LIKE lower('%$bit%') OR lower(u.firstname) LIKE lower('%$bit%') OR lower(u.lastname) LIKE lower('%$bit%')";
        }
      }
    } else {
      $OR_CLAUSE = "lower(u.email) LIKE lower('%$searchPattern%') OR lower(u.firstname) LIKE lower('%$searchPattern%') OR lower(u.lastname) LIKE lower('%$searchPattern%')";
    }

    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS et.* FROM $this->tableName et, " . DB_TABLE_USER . " u WHERE et.user_account_id = u.id AND ($OR_CLAUSE) ORDER BY u.firstname, u.lastname";
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

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS et.* FROM $this->tableName et, " . DB_TABLE_USER . " u WHERE et.user_account_id = u.id ORDER BY u.firstname, u.lastname";
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

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
