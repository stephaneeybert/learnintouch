<?php

class NewsStoryImageDao extends Dao {

  var $tableName;

  function NewsStoryImageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
image varchar(255),
description varchar(255),
list_order int unsigned not null,
news_story_id int unsigned not null,
index (news_story_id), foreign key (news_story_id) references news_story(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($image, $description, $listOrder, $newsStoryId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$image', '$description', '$listOrder', '$newsStoryId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $image, $description, $listOrder, $newsStoryId) {
    $sqlStatement = "UPDATE $this->tableName SET image = '$image', description = '$description', list_order = '$listOrder', news_story_id = '$newsStoryId' WHERE id = '$id'";
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

  function selectByNextListOrder($newsStoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($newsStoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($newsStoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsStoryId($newsStoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsStoryIdOrderById($newsStoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_story_id = '$newsStoryId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectExcerptLikeImage($image) {
    $sqlStatement = "SELECT * FROM " . DB_TABLE_NEWS_STORY . " WHERE excerpt LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function selectParagraphLikeImage($image) {
    $sqlStatement = "SELECT * FROM " . DB_TABLE_NEWS_STORY_PARAGRAPH . " WHERE header LIKE '%$image%' OR body LIKE '%$image%' OR footer LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($newsStoryId) {
    $sqlStatement = "SELECT count(distinct ni1.id) as count FROM $this->tableName ni1, $this->tableName ni2 where ni1.id != ni2.id and ni1.news_story_id = ni2.news_story_id and ni1.list_order = ni2.list_order and ni1.news_story_id = $newsStoryId";
    return($this->querySelect($sqlStatement));
  }

}

?>
