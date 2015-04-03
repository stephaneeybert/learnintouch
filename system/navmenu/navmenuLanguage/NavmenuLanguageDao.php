<?php

class NavmenuLanguageDao extends Dao {

  var $tableName;

  function NavmenuLanguageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
language_code varchar(2),
navmenu_id int unsigned not null,
index (navmenu_id), foreign key (navmenu_id) references navmenu(id),
navmenu_item_id int unsigned not null,
index (navmenu_item_id), foreign key (navmenu_item_id) references navmenu_item(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($language, $navmenuId, $navmenuItemId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$language', '$navmenuId', '$navmenuItemId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $language, $navmenuId, $navmenuItemId) {
    $sqlStatement = "UPDATE $this->tableName SET language_code = '$language', navmenu_id = '$navmenuId', navmenu_item_id = '$navmenuItemId' WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavmenuId($navmenuId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navmenu_id = '$navmenuId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavmenuItemId($navmenuItemId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navmenu_item_id = '$navmenuItemId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavmenuIdAndNoLanguage($navmenuId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navmenu_id = '$navmenuId' AND (language_code = '0' OR language_code = '') LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavmenuIdAndLanguage($navmenuId, $language) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navmenu_id = '$navmenuId' AND language_code = '$language' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
