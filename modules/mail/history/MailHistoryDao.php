<?php

class MailHistoryDao extends Dao {

  var $tableName;

  function MailHistoryDao($dataSource, $tableName) {
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
attachments text,
mail_list_id int unsigned,
index (mail_list_id), foreign key (mail_list_id) references mail_list(id),
email varchar(255),
admin_id int unsigned,
index (admin_id), foreign key (admin_id) references admin(id),
send_datetime datetime not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($subject, $body, $description, $attachments, $mailListId, $email, $adminId, $sendDate) {
    $mailListId = LibString::emptyToNULL($mailListId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$subject', '$body', '$description', '$attachments', $mailListId, '$email', $adminId, '$sendDate')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $subject, $body, $description, $attachments, $mailListId, $email, $adminId, $sendDate) {
    $mailListId = LibString::emptyToNULL($mailListId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "UPDATE $this->tableName SET subject = '$subject', body = '$body', description = '$description', attachments = '$attachments', mail_list_id = $mailListId, email = '$email', admin_id = $adminId, send_datetime = '$sendDate' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteAll() {
    $sqlStatement = "DELETE FROM $this->tableName";
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

  function selectByAdminId($adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE admin_id = '$adminId' OR (admin_id IS NULL AND '$adminId' < '1') ORDER BY send_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListId($mailListId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mail_list_id = '$mailListId' OR (mail_list_id IS NULL AND '$mailListId' < '1') ORDER BY send_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY send_datetime DESC";
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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(subject) LIKE lower('%$searchPattern%') OR lower(body) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') ORDER BY send_datetime DESC";
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

}

?>
