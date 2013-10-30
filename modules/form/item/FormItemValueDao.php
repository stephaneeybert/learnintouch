<?php

class FormItemValueDao extends Dao {

  var $tableName;

  function FormItemValueDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
value varchar(50),
text text,
form_item_id int unsigned not null,
index (form_item_id), foreign key (form_item_id) references form_item(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($value, $text, $formItemId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$value', '$text', '$formItemId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $value, $text, $formItemId) {
    $sqlStatement = "UPDATE $this->tableName SET value = '$value', text = '$text', form_item_id = '$formItemId' WHERE id = '$id'";
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
