<?php

class ElearningAnswerDao extends Dao {

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
answer varchar(255),
explanation text,
image varchar(255),
audio varchar(255),
elearning_question_id int unsigned not null,
index (elearning_question_id), foreign key (elearning_question_id) references elearning_question(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($answer, $explanation, $image, $audio, $elearningQuestion, $listOrder) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$answer', '$explanation', '$image', '$audio', '$elearningQuestion', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $answer, $explanation, $image, $audio, $elearningQuestion, $listOrder) {
    $sqlStatement = "UPDATE $this->tableName SET answer = '$answer', explanation = '$explanation', image = '$image', audio = '$audio', elearning_question_id = '$elearningQuestion', list_order = '$listOrder' WHERE id = '$id'";
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

  function selectByNextListOrder($elearningAnswerId, $elearningQuestion, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' AND list_order >= '$listOrder' AND id != '$elearningAnswerId' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($elearningAnswerId, $elearningQuestion, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' AND list_order <= '$listOrder' AND id != '$elearningAnswerId' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($elearningQuestion, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestionAndAnswer($elearningQuestion, $answer) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' AND answer = '$answer' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestion($elearningQuestion) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestionOrderById($elearningQuestion) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestion' ORDER BY id";
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

  function countDuplicateListOrderRows($elearningQuestionId) {
    $sqlStatement = "SELECT count(distinct ea1.id) as count FROM $this->tableName ea1, $this->tableName ea2 where ea1.id != ea2.id and ea1.elearning_question_id = ea2.elearning_question_id and ea1.list_order = ea2.list_order and ea1.elearning_question_id = $elearningQuestionId";
    return($this->querySelect($sqlStatement));
  }

}

?>
