<?php

class SmsDao extends Dao {

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
body text not null,
description varchar(255),
admin_id int unsigned,
index (admin_id), foreign key (admin_id) references admin(id),
category_id int unsigned,
index (category_id), foreign key (category_id) references sms_category(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($body, $description, $adminId, $categoryId) {
    $adminId = LibString::emptyToNULL($adminId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$body', '$description', $adminId, $categoryId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $body, $description, $adminId, $categoryId) {
    $adminId = LibString::emptyToNULL($adminId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "UPDATE $this->tableName SET body = '$body', description = '$description', admin_id = $adminId, category_id = $categoryId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
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

  function selectByAdminId($adminId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE admin_id = '$adminId' OR (admin_id IS NULL AND '$adminId' < '1') ORDER BY body";
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

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY body";
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

  function selectByAdminIdAndCategoryId($adminId, $categoryId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (admin_id = '$adminId' OR (admin_id IS NULL AND '$adminId' < '1')) AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) ORDER BY body";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY body";
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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s LEFT JOIN " . DB_TABLE_ADMIN . " a ON (s.admin_id = a.id) LEFT JOIN " . DB_TABLE_SMS_CATEGORY . " c ON s.category_id = c.id WHERE lower(s.body) LIKE lower('%$searchPattern%') OR lower(s.description) LIKE lower('%$searchPattern%') OR lower(a.firstname) LIKE lower('%$searchPattern%') OR lower(a.lastname) LIKE lower('%$searchPattern%') OR lower(a.login) LIKE lower('%$searchPattern%') OR lower(a.email) LIKE lower('%$searchPattern%') OR lower(c.name) LIKE lower('%$searchPattern%') ORDER BY s.body";
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
