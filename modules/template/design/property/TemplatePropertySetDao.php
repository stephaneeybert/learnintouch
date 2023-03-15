<?php

class TemplatePropertySetDao extends Dao {

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
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert() {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '')";
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

  function cleanup() {
    $sqlStatement = "delete from template_property_set where id not in (select template_property_set_id from template_model) and id not in (select inner_template_property_set_id from template_model) and id not in (select template_property_set_id from template_container) and id not in (select template_property_set_id from template_page_tag) and id not in (select template_property_set_id from template_element_tag)";
    return($this->querySelect($sqlStatement));
  }

}

?>
