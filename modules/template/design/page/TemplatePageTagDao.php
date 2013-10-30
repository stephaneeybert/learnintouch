<?php

class TemplatePageTagDao extends Dao {

  var $tableName;

  function TemplatePageTagDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
template_page_id int unsigned not null,
index (template_page_id), foreign key (template_page_id) references template_page(id),
template_property_set_id int unsigned,
index (template_property_set_id), foreign key (template_property_set_id) references template_property_set(id),
dom_tag_id varchar(50) not null,
unique (template_page_id, dom_tag_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($templatePageId, $templatePropertySetId, $tagID) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$templatePageId', $templatePropertySetId, '$tagID')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $templatePageId, $templatePropertySetId, $tagID) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "UPDATE $this->tableName SET template_page_id = '$templatePageId', template_property_set_id = $templatePropertySetId, dom_tag_id = '$tagID'  WHERE id = '$id'";
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

  function selectByTemplatePageId($templatePageId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_page_id = '$templatePageId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplatePageIdAndTagID($templatePageId, $tagID) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_page_id = '$templatePageId' AND dom_tag_id = '$tagID'";
    return($this->querySelect($sqlStatement));
  }

}

?>
