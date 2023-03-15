<?php

class ElearningLessonHeadingDao extends Dao {

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
content text,
list_order int unsigned not null,
image varchar(255),
elearning_lesson_model_id int unsigned,
index (elearning_lesson_model_id), foreign key (elearning_lesson_model_id) references elearning_lesson_model(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $content, $listOrder, $image, $elearningLessonModelId) {
    $elearningLessonModelId = LibString::emptyToNULL($elearningLessonModelId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$content', '$listOrder', '$image', $elearningLessonModelId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $content, $listOrder, $image, $elearningLessonModelId) {
    $elearningLessonModelId = LibString::emptyToNULL($elearningLessonModelId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', content = '$content', list_order = '$listOrder', image = '$image', elearning_lesson_model_id = $elearningLessonModelId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($listOrder, $elearningLessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_model_id = '$elearningLessonModelId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($listOrder, $elearningLessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_model_id = '$elearningLessonModelId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($listOrder, $elearningLessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_model_id = '$elearningLessonModelId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByElearningLessonModelId($elearningLessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_model_id = '$elearningLessonModelId' OR (elearning_lesson_model_id IS NULL AND '$elearningLessonModelId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByElearningLessonModelIdOrderById($elearningLessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_model_id = '$elearningLessonModelId' OR (elearning_lesson_model_id IS NULL AND '$elearningLessonModelId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($elearningLessonModelId) {
    $sqlStatement = "SELECT count(distinct nh1.id) as count FROM $this->tableName nh1, $this->tableName nh2 where nh1.id != nh2.id and nh1.elearning_lesson_model_id = nh2.elearning_lesson_model_id and nh1.list_order = nh2.list_order and nh1.elearning_lesson_model_id = $elearningLessonModelId";
    return($this->querySelect($sqlStatement));
  }

}

?>
