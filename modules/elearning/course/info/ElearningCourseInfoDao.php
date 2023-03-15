<?php

class ElearningCourseInfoDao extends Dao {

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
headline varchar(255),
information text,
list_order int unsigned not null,
elearning_course_id int unsigned,
index (elearning_course_id), foreign key (elearning_course_id) references elearning_course(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($headline, $information, $listOrder, $elearningCourseId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$headline', '$information', '$listOrder', '$elearningCourseId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $headline, $information, $listOrder, $elearningCourseId) {
    $sqlStatement = "UPDATE $this->tableName SET headline = '$headline', information = '$information', list_order = '$listOrder', elearning_course_id = '$elearningCourseId' WHERE id = '$id'";
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

  function selectByCourseId($elearningCourseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdOrderById($elearningCourseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($elearningCourseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($elearningCourseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($elearningCourseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($elearningCourseId) {
    $sqlStatement = "SELECT count(distinct eci1.id) as count FROM $this->tableName eci1, $this->tableName eci2 where eci1.id != eci2.id and eci1.elearning_course_id = eci2.elearning_course_id and eci1.list_order = eci2.list_order and eci1.elearning_course_id = $elearningCourseId";
    return($this->querySelect($sqlStatement));
  }

}

?>
