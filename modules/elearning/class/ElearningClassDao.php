<?php

class ElearningClassDao extends Dao {

  var $tableName;

  function ElearningClassDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

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
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description' WHERE id = '$id'";
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

  function selectBySubscriptionWithTeacherId($teacherId) {
    $sqlStatement = "SELECT ec.* FROM $this->tableName ec, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es WHERE es.teacher_id = '$teacherId' AND es.class_id = ec.id ORDER BY ec.name";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

}

?>
