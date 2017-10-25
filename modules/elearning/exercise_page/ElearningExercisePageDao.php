<?php

class ElearningExercisePageDao extends Dao {

  var $tableName;

  function ElearningExercisePageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(255),
description varchar(255),
instructions text,
text text,
hide_text boolean not null,
text_max_height int unsigned not null,
image varchar(255),
audio varchar(255),
autostart boolean not null,
video varchar(1024),
video_url varchar(255),
question_type varchar(50),
hint_placement varchar(50),
elearning_exercise_id int unsigned not null,
index (elearning_exercise_id), foreign key (elearning_exercise_id) references elearning_exercise(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $instructions, $text, $hideText, $textMaxHeigth, $image, $audio, $autostart, $video, $videoUrl, $questionType, $hintPlacement, $elearningExerciseId, $listOrder) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$instructions', '$text', '$hideText', '$textMaxHeigth', '$image', '$audio', '$autostart', '$video', '$videoUrl', '$questionType', '$hintPlacement', '$elearningExerciseId', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $instructions, $text, $hideText, $textMaxHeigth, $image, $audio, $autostart, $video, $videoUrl, $questionType, $hintPlacement, $elearningExerciseId, $listOrder) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', instructions = '$instructions', text = '$text', hide_text = '$hideText', text_max_height = '$textMaxHeigth', image = '$image', audio = '$audio', autostart = '$autostart', video = '$video', video_url = '$videoUrl', question_type = '$questionType', hint_placement = '$hintPlacement', elearning_exercise_id = '$elearningExerciseId', list_order = '$listOrder' WHERE id = '$id'";
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

  function selectByExerciseId($elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByExerciseIdOrderById($elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($elearningExerciseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($elearningExerciseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($elearningExerciseId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectTextLikeImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE text LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAudio($audio) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE audio = '$audio'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($elearningExerciseId) {
    $sqlStatement = "SELECT count(distinct eep1.id) as count FROM $this->tableName eep1, $this->tableName eep2 where eep1.id != eep2.id and eep1.elearning_exercise_id = eep2.elearning_exercise_id and eep1.list_order = eep2.list_order and eep1.elearning_exercise_id = $elearningExerciseId";
    return($this->querySelect($sqlStatement));
  }

}

?>
