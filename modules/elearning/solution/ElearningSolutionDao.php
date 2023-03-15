<?php

class ElearningSolutionDao extends Dao {

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
elearning_question_id int unsigned not null,
index (elearning_question_id), foreign key (elearning_question_id) references elearning_question(id),
elearning_answer_id int unsigned not null,
index (elearning_answer_id), foreign key (elearning_answer_id) references elearning_answer(id),
unique (elearning_question_id, elearning_answer_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningQuestion, $elearningAnswer) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elearningQuestion', '$elearningAnswer')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningQuestion, $elearningAnswer) {
    $sqlStatement = "UPDATE $this->tableName SET elearning_question_id = '$elearningQuestion', elearning_answer_id = '$elearningAnswer' WHERE id = '$id'";
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

  function selectByQuestion($elearningQuestion) {
    $sqlStatement = "SELECT s.* FROM $this->tableName s, " . DB_TABLE_ELEARNING_ANSWER . " a WHERE s.elearning_answer_id = a.id AND s.elearning_question_id = '$elearningQuestion' ORDER BY a.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByAnswer($elearningAnswer) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_answer_id = '$elearningAnswer'";
    return($this->querySelect($sqlStatement));
  }

  function selectByQuestionAndAnswer($elearningQuestion, $elearningAnswer) {
    $sqlStatement = "SELECT s.* FROM $this->tableName s, " . DB_TABLE_ELEARNING_ANSWER . " a WHERE s.elearning_answer_id = a.id AND s.elearning_question_id = '$elearningQuestion' AND s.elearning_answer_id = '$elearningAnswer' ORDER BY a.list_order";
    return($this->querySelect($sqlStatement));
  }

}

?>
