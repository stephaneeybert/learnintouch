<?php

class SmsListUserDao extends Dao {

  var $tableName;

  function SmsListUserDao($dataSource, $tableName) {
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
user_account_id int unsigned not null,
index (user_account_id), foreign key (user_account_id) references user(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($smsListId, $userId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$smsListId', '$userId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $smsListId, $userId) {
    $sqlStatement = "UPDATE $this->tableName SET sms_list_id = '$smsListId', user_account_id = '$userId' WHERE id = '$id'";
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
    $sqlStatement = "SELECT m.* FROM $this->tableName m, " . DB_TABLE_USER . " u WHERE sms_list_id = '$smsListId' and u.id = m.user_account_id order by u.lastname, u.firstname";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId'";
    return($this->querySelect($sqlStatement));
  }

  function selectBySmsListIdAndUserId($smsListId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_list_id = '$smsListId' AND user_account_id = '$userId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
