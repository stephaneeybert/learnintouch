<?php

class TemplatePageDao extends Dao {

  var $tableName;

  function TemplatePageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
system_page varchar(50) not null,
template_model_id int unsigned not null,
index (template_model_id), foreign key (template_model_id) references template_model(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($systemPage, $templateModelId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$systemPage', '$templateModelId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $systemPage, $templateModelId) {
    $sqlStatement = "UPDATE $this->tableName SET system_page = '$systemPage', template_model_id = '$templateModelId' WHERE id = '$id'";
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

  function selectByTemplateModelId($templateModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' AND system_page = '$systemPage' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
