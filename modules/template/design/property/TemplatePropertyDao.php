<?php

class TemplatePropertyDao extends Dao {

  var $tableName;

  function TemplatePropertyDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50) not null,
value varchar(50),
template_property_set_id int unsigned not null,
index (template_property_set_id), foreign key (template_property_set_id) references template_property_set(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $value, $templatePropertySetId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$value', '$templatePropertySetId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $value, $templatePropertySetId) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', value = '$value', template_property_set_id = '$templatePropertySetId' WHERE id = '$id'";
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

  function selectByValue($value) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE value = '$value'";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplatePropertySetIdAndName($templatePropertySetId, $name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (template_property_set_id = '$templatePropertySetId' OR (template_property_set_id IS NULL AND '$templatePropertySetId' < '1')) AND name = '$name'";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplatePropertySetId($templatePropertySetId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_property_set_id = '$templatePropertySetId' OR (template_property_set_id IS NULL AND '$templatePropertySetId' < '1') ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function cleanup() {
    $sqlStatement = "delete from template_property where template_property_set_id not in (select template_property_set_id from template_model) and template_property_set_id not in (select inner_template_property_set_id from template_model) and template_property_set_id not in (select template_property_set_id from template_container) and template_property_set_id not in (select template_property_set_id from template_page_tag) and template_property_set_id not in (select template_property_set_id from template_element_tag)";
    return($this->querySelect($sqlStatement));
  }

}

?>
