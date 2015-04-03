<?php

class MailDao extends Dao {

  var $tableName;

  function MailDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
subject varchar(255) not null,
body longtext,
description varchar(255),
text_format boolean not null,
attachments text,
creation_datetime datetime,
send_datetime datetime,
locked boolean not null,
admin_id int unsigned,
index (admin_id), foreign key (admin_id) references admin(id),
category_id int unsigned,
index (category_id), foreign key (category_id) references mail_category(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($subject, $body, $description, $textFormat, $attachments, $creationDateTime, $sendDateTime, $locked, $adminId, $categoryId) {
    $adminId = LibString::emptyToNULL($adminId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $sendDateTime = LibString::emptyToNULL($sendDateTime);
    $sendDateTime = LibString::addSingleQuotesIfNotNULL($sendDateTime);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$subject', '$body', '$description', '$textFormat', '$attachments', '$creationDateTime', $sendDateTime, '$locked', $adminId, $categoryId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $subject, $body, $description, $textFormat, $attachments, $creationDateTime, $sendDateTime, $locked, $adminId, $categoryId) {
    $adminId = LibString::emptyToNULL($adminId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $sendDateTime = LibString::emptyToNULL($sendDateTime);
    $sendDateTime = LibString::addSingleQuotesIfNotNULL($sendDateTime);
    $sqlStatement = "UPDATE $this->tableName SET subject = '$subject', body = '$body', description = '$description', text_format = '$textFormat', attachments = '$attachments', creation_datetime = '$creationDateTime', send_datetime = $sendDateTime, locked = '$locked', admin_id = $adminId, category_id = $categoryId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY subject";
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

  function selectByAdminId($adminId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE admin_id = '$adminId' OR (admin_id IS NULL AND '$adminId' < '1') ORDER BY subject";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (admin_id = '$adminId' OR (admin_id IS NULL AND '$adminId' < '1')) AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) ORDER BY subject";
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

  function deleteByDate($sinceDate) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE send_datetime IS NOT NULL AND send_datetime <= '$sinceDate'";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS m.* FROM $this->tableName m LEFT JOIN " . DB_TABLE_ADMIN . " a ON (m.admin_id = a.id) LEFT JOIN " . DB_TABLE_MAIL_CATEGORY . " c ON (m.category_id = c.id) WHERE lower(m.subject) LIKE lower('%$searchPattern%') OR lower(m.body) LIKE lower('%$searchPattern%') OR lower(m.description) LIKE lower('%$searchPattern%') OR lower(a.firstname) LIKE lower('%$searchPattern%') OR lower(a.lastname) LIKE lower('%$searchPattern%') OR lower(a.login) LIKE lower('%$searchPattern%') OR lower(a.email) LIKE lower('%$searchPattern%') OR lower(c.name) LIKE lower('%$searchPattern%') ORDER BY m.subject";
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

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY subject";
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

  function selectBodyLikeImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE body LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function selectAttachmentsLikeFile($file) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE attachments LIKE '%$file%'";
    return($this->querySelect($sqlStatement));
  }

}

?>
