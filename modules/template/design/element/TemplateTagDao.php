<?php

class TemplateTagDao extends Dao {

  var $tableName;

  function TemplateTagDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
template_element_id int unsigned not null,
index (template_element_id), foreign key (template_element_id) references template_element(id),
template_property_set_id int unsigned,
index (template_property_set_id), foreign key (template_property_set_id) references template_property_set(id),
dom_tag_id varchar(50) not null,
unique (template_element_id, dom_tag_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($templateElementId, $templatePropertySetId, $tagID) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$templateElementId', $templatePropertySetId, '$tagID')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $templateElementId, $templatePropertySetId, $tagID) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "UPDATE $this->tableName SET template_element_id = '$templateElementId', template_property_set_id = $templatePropertySetId, dom_tag_id = '$tagID'  WHERE id = '$id'";
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

  function selectByTemplateElementId($templateElementId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_element_id = '$templateElementId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateElementIdAndTagID($templateElementId, $tagID) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_element_id = '$templateElementId' AND dom_tag_id = '$tagID'";
    return($this->querySelect($sqlStatement));
  }

}

?>
