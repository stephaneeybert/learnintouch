<?php

class ElearningSessionCourseDao extends Dao {

  var $tableName;

  function ElearningSessionCourseDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
elearning_session_id int unsigned not null,
index (elearning_session_id), foreign key (elearning_session_id) references elearning_session(id),
elearning_course_id int unsigned not null,
index (elearning_course_id), foreign key (elearning_course_id) references elearning_course(id),
unique (elearning_session_id, elearning_course_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningSessionId, $elearningCourseId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elearningSessionId', '$elearningCourseId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningSessionId, $elearningCourseId) {
    $sqlStatement = "UPDATE $this->tableName SET elearning_session_id = '$elearningSessionId', elearning_course_id = '$elearningCourseId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteBySessionId($elearningSessionId) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE elearning_session_id = '$elearningSessionId'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionId($elearningSessionId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_session_id = '$elearningSessionId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseId($elearningCourseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId'";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_session_id = '$elearningSessionId' AND elearning_course_id = '$elearningCourseId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
