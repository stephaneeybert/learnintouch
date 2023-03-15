<?php

class ContactDao extends Dao {

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
email varchar(255) not null,
organisation varchar(255),
telephone varchar(20),
subject varchar(255),
message text not null,
contact_datetime datetime,
contact_status_id int unsigned,
index (contact_status_id), foreign key (contact_status_id) references contact_status(id),
contact_referer_id int unsigned,
index (contact_referer_id), foreign key (contact_referer_id) references contact_referer(id),
garbage boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $email, $organisation, $telephone, $subject, $message, $contactDate, $status, $contactRefererId, $garbage) {
    $status = LibString::emptyToNULL($status);
    $contactRefererId = LibString::emptyToNULL($contactRefererId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$email', '$organisation', '$telephone', '$subject', '$message', '$contactDate', $status, $contactRefererId, '$garbage')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $email, $organisation, $telephone, $subject, $message, $contactDate, $status, $contactRefererId, $garbage) {
    $status = LibString::emptyToNULL($status);
    $contactRefererId = LibString::emptyToNULL($contactRefererId);
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', email = '$email', organisation = '$organisation', telephone = '$telephone', subject = '$subject', message = '$message', contact_datetime = '$contactDate', contact_status_id = $status, contact_referer_id = $contactRefererId, garbage = '$garbage' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteByDate($sinceDate) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE contact_datetime IS NOT NULL AND contact_datetime <= '$sinceDate'";
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

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY contact_datetime DESC";
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

  function selectNonGarbage($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' ORDER BY contact_datetime DESC";
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

  function selectByStatus($contactStatusId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (contact_status_id = '$contactStatusId' OR (contact_status_id IS NULL AND '$contactStatusId' < '1')) ORDER BY contact_datetime DESC";
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

  function selectAllByStatusId($contactStatusId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (contact_status_id = '$contactStatusId' OR (contact_status_id IS NULL AND '$contactStatusId' < '1')) ORDER BY contact_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectAllByRefererId($contactRefereId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (contact_referer_id = '$contactRefereId' OR (contact_referer_id IS NULL AND '$contactRefereId' < '1')) ORDER BY contact_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectGarbage($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage = '1' ORDER BY contact_datetime DESC";
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
