<?php

class ElearningCourseItemDao extends Dao {

  var $tableName;

  function ElearningCourseItemDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
elearning_course_id int unsigned not null,
index (elearning_course_id), foreign key (elearning_course_id) references elearning_course(id),
elearning_exercise_id int unsigned,
index (elearning_exercise_id), foreign key (elearning_exercise_id) references elearning_exercise(id),
elearning_lesson_id int unsigned,
index (elearning_lesson_id), foreign key (elearning_lesson_id) references elearning_lesson(id),
list_order int unsigned not null,
unique (elearning_course_id, elearning_exercise_id),
unique (elearning_course_id, elearning_lesson_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningCourseId, $elearningExerciseId, $elearningLessonId, $listOrder) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $elearningLessonId = LibString::emptyToNULL($elearningLessonId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elearningCourseId', $elearningExerciseId, $elearningLessonId, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningCourseId, $elearningExerciseId, $elearningLessonId, $listOrder) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $elearningLessonId = LibString::emptyToNULL($elearningLessonId);
    $sqlStatement = "UPDATE $this->tableName SET elearning_course_id = '$elearningCourseId', elearning_exercise_id = $elearningExerciseId, elearning_lesson_id = $elearningLessonId, list_order = '$listOrder' WHERE id = '$id'";
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

  function selectByCourseId($elearningCourseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' ORDER BY list_order";
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

  function selectByCourseIdOrderById($elearningCourseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByExerciseId($elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (elearning_exercise_id = '$elearningExerciseId' OR (elearning_exercise_id IS NULL AND '$elearningExerciseId' < '1')) ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonId($elearningLessonId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (elearning_lesson_id = '$elearningLessonId' OR (elearning_lesson_id IS NULL AND '$elearningLessonId' < '1')) ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' AND (elearning_exercise_id = '$elearningExerciseId' OR (elearning_exercise_id IS NULL AND '$elearningExerciseId' < '1')) LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndLessonExerciseId($elearningCourseId, $elearningExerciseId) {
    $sqlStatement = "SELECT DISTINCT eci.* FROM $this->tableName eci, " . DB_TABLE_ELEARNING_LESSON . " el, " . DB_TABLE_ELEARNING_LESSON_PARAGRAPH . " elp  WHERE el.id = eci.elearning_lesson_id AND elp.elearning_lesson_id = el.id AND eci.elearning_course_id = '$elearningCourseId' AND (elp.elearning_exercise_id = '$elearningExerciseId' OR (elp.elearning_exercise_id IS NULL AND '$elearningExerciseId' < '1')) LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_course_id = '$elearningCourseId' AND (elearning_lesson_id = '$elearningLessonId' OR (elearning_lesson_id IS NULL AND '$elearningLessonId' < '1')) LIMIT 1";
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
