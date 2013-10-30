<?php

class ElearningScoringRangeDao extends Dao {

  var $tableName;

  function ElearningScoringRangeDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
upper_range int unsigned not null,
score text,
advice text,
proposal text,
link_text varchar(255),
link_url varchar(255),
elearning_scoring_id int unsigned not null,
index (elearning_scoring_id), foreign key (elearning_scoring_id) references elearning_scoring(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($upperRange, $score, $advice, $proposal, $linkText, $linkUrl, $scoringId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$upperRange', '$score', '$advice', '$proposal', '$linkText', '$linkUrl', '$scoringId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $upperRange, $score, $advice, $proposal, $linkText, $linkUrl, $scoringId) {
    $sqlStatement = "UPDATE $this->tableName SET upper_range = '$upperRange', score = '$score', advice = '$advice', proposal = '$proposal', link_text = '$linkText', link_url = '$linkUrl', elearning_scoring_id = '$scoringId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteByScoringId($scoringId) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE elearning_scoring_id = '$scoringId'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByScoringId($scoringId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_scoring_id = '$scoringId' ORDER BY upper_range";
    return($this->querySelect($sqlStatement));
  }

}

?>
