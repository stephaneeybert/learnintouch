<?php

class ElearningQuestionDao extends Dao {

  var $tableName;

  function ElearningQuestionDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
question text,
explanation text,
elearning_exercise_page_id int unsigned not null,
index (elearning_exercise_page_id), foreign key (elearning_exercise_page_id) references elearning_exercise_page(id),
image varchar(255),
audio varchar(255),
hint text,
points int unsigned,
answer_nb_words int unsigned,
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($question, $explanation, $elearningExercisePageId, $image, $audio, $hint, $points, $answerNbWords, $listOrder) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$question', '$explanation', '$elearningExercisePageId', '$image', '$audio', '$hint', '$points', '$answerNbWords', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $question, $explanation, $elearningExercisePageId, $image, $audio, $hint, $points, $answerNbWords, $listOrder) {
    $sqlStatement = "UPDATE $this->tableName SET question = '$question', explanation = '$explanation', elearning_exercise_page_id = '$elearningExercisePageId', image = '$image', audio = '$audio', hint = '$hint', points = '$points', answer_nb_words = '$answerNbWords', list_order = '$listOrder' WHERE id = '$id'";
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

  function selectByExercisePage($elearningExercisePageId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_page_id = '$elearningExercisePageId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByExercisePageOrderById($elearningExercisePageId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_page_id = '$elearningExercisePageId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByExercise($elearningExerciseId) {
    $sqlStatement = "SELECT eq.* FROM $this->tableName eq, " . DB_TABLE_ELEARNING_EXERCISE_PAGE . " ep, " . DB_TABLE_ELEARNING_EXERCISE . " ee WHERE eq.elearning_exercise_page_id = ep.id AND ep.elearning_exercise_id = ee.id AND ee.id = '$elearningExerciseId' ORDER BY ep.list_order, eq.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($elearningExercisePageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_page_id = '$elearningExercisePageId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($elearningExercisePageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_page_id = '$elearningExercisePageId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($elearningExercisePageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_page_id = '$elearningExercisePageId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAudio($audio) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE audio = '$audio'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($elearningExercisePageId) {
    $sqlStatement = "SELECT count(distinct eq1.id) as count FROM $this->tableName eq1, $this->tableName eq2 where eq1.id != eq2.id and eq1.elearning_exercise_page_id = eq2.elearning_exercise_page_id and eq1.list_order = eq2.list_order and eq1.elearning_exercise_page_id = $elearningExercisePageId";
    return($this->querySelect($sqlStatement));
  }

}

?>
