<?php

class SmsHistoryDao extends Dao {

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
sms_id int unsigned not null,
index (sms_id), foreign key (sms_id) references sms(id),
sms_list_id int unsigned,
index (sms_list_id), foreign key (sms_list_id) references sms_list(id),
mobile_phone varchar(50),
admin_id int unsigned,
index (admin_id), foreign key (admin_id) references admin(id),
send_datetime datetime,
nb_recipients int unsigned,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($smsId, $smsListId, $mobilePhone, $adminId, $sendDate, $nbRecipients) {
    $smsListId = LibString::emptyToNULL($smsListId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$smsId', $smsListId, '$mobilePhone', $adminId, '$sendDate', '$nbRecipients')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $smsId, $smsListId, $mobilePhone, $adminId, $sendDate, $nbRecipients) {
    $smsListId = LibString::emptyToNULL($smsListId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "UPDATE $this->tableName SET sms_id = '$smsId', sms_list_id = $smsListId, mobile_phone = '$mobilePhone', admin_id = $adminId, send_datetime = '$sendDate', nb_recipients = '$nbRecipients' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
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

  function selectBySmsListId($smsListId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_list_id = '$smsListId' OR (sms_list_id IS NULL AND '$smsListId' < '1') ORDER BY send_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectBySmsId($smsId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_id = '$smsId' OR (sms_id IS NULL AND '$smsId' < '1') ORDER BY send_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
