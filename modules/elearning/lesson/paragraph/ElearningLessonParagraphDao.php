<?php

class ElearningLessonParagraphDao extends Dao {

  var $tableName;

  function ElearningLessonParagraphDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
headline varchar(255) not null,
body text,
image varchar(255),
audio varchar(255),
video varchar(1024),
video_url varchar(255),
list_order int unsigned not null,
elearning_lesson_id int unsigned not null,
index (elearning_lesson_id), foreign key (elearning_lesson_id) references elearning_lesson(id),
elearning_lesson_heading_id int unsigned,
index (elearning_lesson_heading_id), foreign key (elearning_lesson_heading_id) references elearning_lesson_heading(id),
elearning_exercise_id int unsigned,
index (elearning_exercise_id), foreign key (elearning_exercise_id) references elearning_exercise(id),
exercise_title varchar(255),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($headline, $body, $image, $audio, $video, $videoUrl, $listOrder, $elearningLessonId, $elearningLessonHeadingId, $elearningExerciseId, $exerciseTitle) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $elearningLessonHeadingId = LibString::emptyToNULL($elearningLessonHeadingId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$headline', '$body', '$image', '$audio', '$video', '$videoUrl', '$listOrder', '$elearningLessonId', $elearningLessonHeadingId, $elearningExerciseId, '$exerciseTitle')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $headline, $body, $image, $audio, $video, $videoUrl, $listOrder, $elearningLessonId, $elearningLessonHeadingId, $elearningExerciseId, $exerciseTitle) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $elearningLessonHeadingId = LibString::emptyToNULL($elearningLessonHeadingId);
    $sqlStatement = "UPDATE $this->tableName SET headline = '$headline', body = '$body', image = '$image', audio = '$audio', video = '$video', video_url = '$videoUrl', list_order = '$listOrder', elearning_lesson_id = '$elearningLessonId', elearning_lesson_heading_id = $elearningLessonHeadingId, elearning_exercise_id = $elearningExerciseId, exercise_title = '$exerciseTitle' WHERE id = '$id'";
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

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectBodyLikeImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE body LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAudio($audio) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE audio = '$audio'";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonId($elearningLessonId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp LEFT JOIN " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh ON elp.elearning_lesson_heading_id = elh.id WHERE elp.elearning_lesson_id = '$elearningLessonId' ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_id = '$elearningLessonId' AND (elearning_lesson_heading_id = '$elearningLessonHeadingId' OR (elearning_lesson_heading_id IS NULL AND '$elearningLessonHeadingId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_id = '$elearningLessonId' AND (elearning_lesson_heading_id = '$elearningLessonHeadingId' OR (elearning_lesson_heading_id IS NULL AND '$elearningLessonHeadingId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_id = '$elearningLessonId' AND (elearning_lesson_heading_id = '$elearningLessonHeadingId' OR (elearning_lesson_heading_id IS NULL AND '$elearningLessonHeadingId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($elearningLessonId, $elearningLessonHeadingId) {
    $sqlStatement = "SELECT count(distinct elp1.id) as count FROM $this->tableName elp1, $this->tableName elp2 where elp1.id != elp2.id and elp1.elearning_lesson_id = elp2.elearning_lesson_id and elp1.elearning_lesson_heading_id = elp2.elearning_lesson_heading_id and elp1.list_order = elp2.list_order and elp1.elearning_lesson_id = $elearningLessonId and elp1.elearning_lesson_heading_id = $elearningLessonHeadingId";
    return($this->querySelect($sqlStatement));
  }

  function selectByExerciseId($elearningExerciseId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp LEFT JOIN " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh ON elp.elearning_lesson_heading_id = elh.id WHERE elp.elearning_exercise_id = '$elearningExerciseId' AND elp.elearning_lesson_heading_id IS NULL ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByOtherLessonExerciseId($elearningExerciseId, $elearningLessonId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp LEFT JOIN " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh ON elp.elearning_lesson_heading_id = elh.id WHERE elp.elearning_exercise_id = '$elearningExerciseId' AND elp.elearning_lesson_id != '$elearningLessonId' AND elp.elearning_lesson_heading_id IS NULL ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonIdAndExerciseId($elearningLessonId, $elearningExerciseId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp LEFT JOIN " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh ON elp.elearning_lesson_heading_id = elh.id WHERE elp.elearning_lesson_id = '$elearningLessonId' AND (elp.elearning_exercise_id = '$elearningExerciseId' OR (elp.elearning_exercise_id IS NULL AND '$elearningExerciseId' < '1')) AND elp.elearning_lesson_heading_id IS NULL ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonHeadingId($elearningLessonHeadingId) {
    $sqlStatement = "SELECT elp.* FROM $this->tableName elp, " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh WHERE elp.elearning_lesson_heading_id = elh.id AND elp.elearning_lesson_heading_id = '$elearningLessonHeadingId' ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp LEFT JOIN " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh ON elp.elearning_lesson_heading_id = elh.id WHERE elp.elearning_lesson_id = '$elearningLessonId' AND (elp.elearning_lesson_heading_id = '$elearningLessonHeadingId' OR (elp.elearning_lesson_heading_id IS NULL AND '$elearningLessonHeadingId' < '1')) ORDER BY elh.list_order, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonIdAndLessonHeadingIdOrderById($elearningLessonId, $elearningLessonHeadingId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_lesson_id = '$elearningLessonId' AND (elearning_lesson_heading_id = '$elearningLessonHeadingId' OR (elearning_lesson_heading_id IS NULL AND '$elearningLessonHeadingId' < '1')) ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByLessonIdAndNoLessonHeading($elearningLessonId) {
    $sqlStatement = "SELECT DISTINCT elp.* FROM $this->tableName elp, " . DB_TABLE_ELEARNING_LESSON . " el WHERE el.id = elp.elearning_lesson_id AND elp.elearning_lesson_id = '$elearningLessonId' AND (elp.elearning_lesson_heading_id IS NULL OR el.lesson_model_id IS NULL) ORDER BY elp.elearning_lesson_id, elp.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectWithInvalidModelHeading($elearningLessonId) {
    // It may occur, although it should not happen, that a paragraph has a heading that does not belong to the model 
    // of his new lesson, in case a paragraph was moved to another lesson, and the paragraph heading was not reset
    $sqlStatement = "SELECT DISTINCT elp.* FROM elearning_lesson_paragraph elp, " . DB_TABLE_ELEARNING_LESSON_HEADING . " elh, " . DB_TABLE_ELEARNING_LESSON . " el, " . DB_TABLE_ELEARNING_LESSON_MODEL . " elm WHERE elp.elearning_lesson_id = '$elearningLessonId' AND elp.elearning_lesson_heading_id = elh.id AND elp.elearning_lesson_id = el.id AND elh.elearning_lesson_model_id != el.lesson_model_id ORDER BY elh.list_order, elp.list_order;";
    return($this->querySelect($sqlStatement));
  }

}

?>
