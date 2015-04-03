<?php

class FormItemDao extends Dao {

  var $tableName;

  function FormItemDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
type varchar(50),
name varchar(50),
text text,
help varchar(255),
default_value varchar(50),
item_size varchar(3),
maxlength varchar(4),
list_order int unsigned not null,
in_mail_address boolean not null,
mail_list_id int unsigned,
index (mail_list_id), foreign key (mail_list_id) references mail_list(id),
form_id int unsigned not null,
index (form_id), foreign key (form_id) references form(id),
index (form_id, list_order),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($type, $name, $text, $help, $defaultValue, $size, $maxlength, $listOrder, $inMailAddress, $mailListId, $formId) {
    $mailListId = LibString::emptyToNULL($mailListId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$type', '$name', '$text', '$help', '$defaultValue', '$size', '$maxlength', '$listOrder', '$inMailAddress', $mailListId, '$formId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $type, $name, $text, $help, $defaultValue, $size, $maxlength, $listOrder, $inMailAddress, $mailListId, $formId) {
    $mailListId = LibString::emptyToNULL($mailListId);
    $sqlStatement = "UPDATE $this->tableName SET type = '$type', name = '$name', text = '$text', help = '$help', default_value = '$defaultValue', item_size = '$size', maxlength = '$maxlength', list_order = '$listOrder', in_mail_address = '$inMailAddress', mail_list_id = $mailListId, form_id = '$formId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByFormId($formId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_id = '$formId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByFormIdOrderById($formId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_id = '$formId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($formId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_id = '$formId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($formId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_id = '$formId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($formId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE form_id = '$formId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($formId) {
    $sqlStatement = "SELECT count(distinct fi1.id) as count FROM $this->tableName fi1, $this->tableName fi2 where fi1.id != fi2.id and fi1.form_id = fi2.form_id and fi1.list_order = fi2.list_order and fi1.form_id = $formId";
    return($this->querySelect($sqlStatement));
  }

}

?>
