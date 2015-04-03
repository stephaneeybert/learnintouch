<?php

class TemplateElementDao extends Dao {

  var $tableName;

  function TemplateElementDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
element_type varchar(50) not null,
object_id int unsigned,
template_container_id int unsigned not null,
index (template_container_id), foreign key (template_container_id) references template_container(id),
list_order int unsigned not null,
hide boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elementType, $objectId, $templateContainerId, $listOrder, $hide) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elementType', '$objectId', '$templateContainerId', '$listOrder', '$hide')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elementType, $objectId, $templateContainerId, $listOrder, $hide) {
    $sqlStatement = "UPDATE $this->tableName SET element_type = '$elementType', object_id = '$objectId', template_container_id = '$templateContainerId', list_order = '$listOrder', hide = '$hide' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateContainerId($templateContainerId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_container_id = '$templateContainerId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateContainerIdOrderById($templateContainerId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_container_id = '$templateContainerId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($templateContainerId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_container_id = '$templateContainerId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($templateContainerId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_container_id = '$templateContainerId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($templateContainerId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_container_id = '$templateContainerId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($templateContainerId) {
    $sqlStatement = "SELECT count(distinct tc1.id) as count FROM $this->tableName tc1, $this->tableName tc2 where tc1.id != tc2.id and tc1.template_container_id = tc2.template_container_id and tc1.list_order = tc2.list_order and tc1.template_container_id = $templateContainerId";
    return($this->querySelect($sqlStatement));
  }

}

?>
