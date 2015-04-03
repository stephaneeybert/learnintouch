<?php

class SmsListNumberDao extends Dao {

  var $tableName;

  function SmsListNumberDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
sms_list_id int unsigned not null,
index (sms_list_id), foreign key (sms_list_id) references sms_list(id),
sms_number_id int unsigned not null,
index (sms_number_id), foreign key (sms_number_id) references sms_number(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($smsListId, $smsNumberId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$smsListId', '$smsNumberId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $smsListId, $smsNumberId) {
    $sqlStatement = "UPDATE $this->tableName SET sms_list_id = '$smsListId', sms_number_id = '$smsNumberId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteBySmsListId($smsListId) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE sms_list_id = '$smsListId'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectBySmsListId($smsListId) {
    // Do not use the Utils class as it activates
    // the common database in its init function()
    // when using the language module
    $sqlStatement = "SELECT sln.* FROM $this->tableName sln, " . DB_TABLE_SMS_NUMBER . " sn WHERE sln.sms_list_id = '$smsListId' and sn.id = sln.sms_number_id order by sn.firstname, sn.lastname";
    return($this->querySelect($sqlStatement));
  }

  function selectBySmsNumberId($smsNumberId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_number_id = '$smsNumberId'";
    return($this->querySelect($sqlStatement));
  }

  function selectBySmsListIdAndSmsNumberId($smsListId, $smsNumberId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_list_id = '$smsListId' AND sms_number_id = '$smsNumberId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
