<?php

class NewsStoryParagraphDao extends Dao {

  var $tableName;

  function NewsStoryParagraphDao($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
header text,
body text,
footer text,
news_story_id int unsigned not null,
index (news_story_id), foreign key (news_story_id) references news_story(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($header, $body, $footer, $newsStoryId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$header', '$body', '$footer', '$newsStoryId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $header, $body, $footer, $newsStoryId) {
    $sqlStatement = "UPDATE $this->tableName SET header = '$header', body = '$body', footer = '$footer', news_story_id = '$newsStoryId' WHERE id = '$id'";
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

  function selectByNewsStoryId($newsStoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

}

?>
