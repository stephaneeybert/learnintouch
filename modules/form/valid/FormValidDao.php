<?php

class FormValidDao extends Dao {

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
type varchar(30) not null,
message text,
boundary varchar(20),
form_item_id int unsigned not null,
index (form_item_id), foreign key (form_item_id) references form_item(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($type, $message, $boundary, $formItemId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$type', '$message', '$boundary', '$formItemId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $type, $message, $boundary, $formItemId) {
    $sqlStatement = "UPDATE $this->tableName SET type = '$type', message = '$message', boundary = '$boundary', form_item_id = '$formItemId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectByFormItemId($formItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_item_id = '$formItemId'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
