<?php

class NavbarLanguageDao extends Dao {

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
navbar_id int unsigned not null,
index (navbar_id), foreign key (navbar_id) references navbar(id),
index (navbar_id, language_code),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($language, $navbarId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$language', '$navbarId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $language, $navbarId) {
    $sqlStatement = "UPDATE $this->tableName SET language_code = '$language', navbar_id = '$navbarId' WHERE id = '$id'";
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

  function selectByNavbarId($navbarId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_id = '$navbarId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavbarIdAndLanguage($navbarId, $language) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_id = '$navbarId' AND language_code = '$language' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavbarIdAndNoLanguage($navbarId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_id = '$navbarId' AND (language_code = '0' OR language_code = '') LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
