<?php

class TemplateModelDao extends Dao {

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
name varchar(50) not null,
unique (name),
description varchar(255),
model_type varchar(50) not null,
parent_id int unsigned,
index (parent_id), foreign key (parent_id) references template_model(id),
template_property_set_id int unsigned,
index (template_property_set_id), foreign key (template_property_set_id) references template_property_set(id),
inner_template_property_set_id int unsigned,
index (inner_template_property_set_id), foreign key (inner_template_property_set_id) references template_property_set(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $modelType, $parentId, $templatePropertySetId, $innerTemplatePropertySetId) {
    $parentId = LibString::emptyToNULL($parentId);
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $innerTemplatePropertySetId = LibString::emptyToNULL($innerTemplatePropertySetId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$modelType', $parentId, $templatePropertySetId, $innerTemplatePropertySetId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $modelType, $parentId, $templatePropertySetId, $innerTemplatePropertySetId) {
    $parentId = LibString::emptyToNULL($parentId);
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $innerTemplatePropertySetId = LibString::emptyToNULL($innerTemplatePropertySetId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', model_type = '$modelType', parent_id = $parentId, template_property_set_id = $templatePropertySetId, inner_template_property_set_id = $innerTemplatePropertySetId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByParentId($parentId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id = '$parentId'";
    return($this->querySelect($sqlStatement));
  }

  function selectWithNoParentAndNotItself($templateModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id IS NULL AND id != '$templateModelId'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

}

?>
