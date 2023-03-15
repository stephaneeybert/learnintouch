<?php

class TemplateElementLanguageDao extends Dao {

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
language_code varchar(2),
object_id int unsigned,
template_element_id int unsigned not null,
index (template_element_id), foreign key (template_element_id) references template_element(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($language, $objectId, $templateElementId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$language', '$objectId', '$templateElementId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $language, $objectId, $templateElementId) {
    $sqlStatement = "UPDATE $this->tableName SET language_code = '$language', object_id = '$objectId', template_element_id = '$templateElementId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateElementId($templateElementId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_element_id = '$templateElementId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByLanguageAndTemplateElementId($language, $templateElementId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE language_code = '$language' AND template_element_id = '$templateElementId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNoLanguageAndTemplateElementId($templateElementId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (language_code = '0' OR language_code = '') AND template_element_id = '$templateElementId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
