<?php

class RssFeedLanguageDao extends Dao {

  var $tableName;

  function RssFeedLanguageDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
language_code varchar(2),
title varchar(50),
url varchar(255),
rss_feed_id int unsigned not null,
index (rss_feed_id), foreign key (rss_feed_id) references rss_feed(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($language, $title, $url, $rssFeedId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$language', '$title', '$url', '$rssFeedId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $language, $title, $url, $rssFeedId) {
    $sqlStatement = "UPDATE $this->tableName SET language_code = '$language', title = '$title', url = '$url', rss_feed_id = '$rssFeedId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectByRssFeedId($rssFeedId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE rss_feed_id = '$rssFeedId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByLanguageAndRssFeedId($language, $rssFeedId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE language_code = '$language' AND rss_feed_id = '$rssFeedId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNoLanguageAndRssFeedId($rssFeedId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (language_code = '0' OR language_code = '') AND rss_feed_id = '$rssFeedId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
