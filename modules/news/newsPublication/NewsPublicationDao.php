<?php

class NewsPublicationDao extends Dao {

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
name varchar(255) not null,
description varchar(255),
nb_columns int unsigned,
slide_down boolean not null,
align varchar(10),
with_archive boolean not null,
with_others boolean not null,
with_by_heading boolean not null,
hide_heading boolean not null,
auto_archive int(3) unsigned,
auto_delete int(3) unsigned,
secured boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $nbColumns, $slideDown, $align, $withArchive, $withOthers, $withByHeading, $hideHeading, $autoArchive, $autoDelete, $secured) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$nbColumns', '$slideDown', '$align', '$withArchive', '$withOthers', '$withByHeading', '$hideHeading', '$autoArchive', '$autoDelete', '$secured')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $nbColumns, $slideDown, $align, $withArchive, $withOthers, $withByHeading, $hideHeading, $autoArchive, $autoDelete, $secured) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', nb_columns = '$nbColumns', slide_down = '$slideDown', align = '$align', with_archive = '$withArchive', with_others = '$withOthers', with_by_heading = '$withByHeading', hide_heading = '$hideHeading', auto_archive = '$autoArchive', auto_delete = '$autoDelete', secured = '$secured' WHERE id = '$id'";
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

  function selectLikePattern($searchPattern) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
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

}

?>
