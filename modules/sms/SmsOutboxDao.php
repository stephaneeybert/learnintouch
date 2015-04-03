<?php

class SmsOutboxDao extends Dao {

  var $tableName;

  function SmsOutboxDao($dataSource, $tableName) {
    $this->Dao($dataSource);

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
email varchar(255),
password varchar(20),
sent boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $mobilePhone, $email, $password, $sent) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$mobilePhone', '$email', '$password', '$sent')";
    return($this->querySelect($sqlStatement));
  }

  function deleteAll() {
    $sqlStatement = "DELETE FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $mobilePhone, $email, $password, $sent) {
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', mobile_phone = '$mobilePhone', email = '$email', password = '$password', sent = '$sent' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function countFailed() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName WHERE sent != '1'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY firstname, lastname, mobile_phone";
    return($this->querySelect($sqlStatement));
  }

  function selectUnsent() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sent != '1' ORDER BY firstname, lastname, mobile_phone";
    return($this->querySelect($sqlStatement));
  }

  function selectSent() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sent = '1' ORDER BY firstname, lastname, mobile_phone";
    return($this->querySelect($sqlStatement));
  }

}

?>
