<?php

class ElearningQuestionResultDao extends Dao {

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
elearning_result_id int unsigned not null,
index (elearning_result_id), foreign key (elearning_result_id) references elearning_result(id),
elearning_question_id int unsigned not null,
index (elearning_question_id), foreign key (elearning_question_id) references elearning_question(id),
elearning_answer_id int unsigned,
index (elearning_answer_id), foreign key (elearning_answer_id) references elearning_answer(id),
elearning_answer_text text,
elearning_answer_order int unsigned,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningResultId, $elearningQuestionId, $elearningAnswerId, $elearningAnswerText, $elearningAnswerOrder) {
    $elearningAnswerId = LibString::emptyToNULL($elearningAnswerId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elearningResultId', '$elearningQuestionId', $elearningAnswerId, '$elearningAnswerText', '$elearningAnswerOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningResultId, $elearningQuestionId, $elearningAnswerId, $elearningAnswerText, $elearningAnswerOrder) {
    $elearningAnswerId = LibString::emptyToNULL($elearningAnswerId);
    $sqlStatement = "UPDATE $this->tableName SET elearning_result_id = '$elearningResultId', elearning_question_id = '$elearningQuestionId', elearning_answer_id = $elearningAnswerId, elearning_answer_text = '$elearningAnswerText', elearning_answer_order = '$elearningAnswerOrder' WHERE id = '$id'";
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

  function selectByResult($elearningResultId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_result_id = '$elearningResultId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByResultAndQuestion($elearningResultId, $elearningQuestionId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_result_id = '$elearningResultId' AND elearning_question_id = '$elearningQuestionId' ORDER BY elearning_answer_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestionId($elearningQuestionId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestionId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestionAndAnswerId($elearningQuestionId, $elearningAnswerId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_question_id = '$elearningQuestionId' AND elearning_answer_id = '$elearningAnswerId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAnswerId($elearningAnswerId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_answer_id = '$elearningAnswerId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $elearningAnswerId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_result_id = '$elearningResultId' AND elearning_question_id = '$elearningQuestionId' AND elearning_answer_id = '$elearningAnswerId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByResultAndExercisePageId($elearningResultId, $elearningExercisePageId) {
    $sqlStatement = "SELECT * FROM $this->tableName er, " . DB_TABLE_ELEARNING_QUESTION . " eq WHERE er.elearning_question_id = eq.id AND er.elearning_result_id = '$elearningResultId' AND eq.elearning_exercise_page_id = '$elearningExercisePageId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
