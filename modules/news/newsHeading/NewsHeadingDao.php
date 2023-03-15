<?php

class NewsHeadingDao extends Dao {

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
list_order int unsigned not null,
name varchar(50) not null,
description varchar(255),
image varchar(255),
news_publication_id int unsigned,
index (news_publication_id), foreign key (news_publication_id) references news_publication(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($listOrder, $name, $description, $image, $newsPublicationId) {
    $newsPublicationId = LibString::emptyToNULL($newsPublicationId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$listOrder', '$name', '$description', '$image', $newsPublicationId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $listOrder, $name, $description, $image, $newsPublicationId) {
    $newsPublicationId = LibString::emptyToNULL($newsPublicationId);
    $sqlStatement = "UPDATE $this->tableName SET list_order = '$listOrder', name = '$name', description = '$description', image = '$image', news_publication_id = $newsPublicationId WHERE id = '$id'";
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

  function selectByNextListOrder($listOrder, $newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($listOrder, $newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($listOrder, $newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPublicationId($newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPublicationIdOrderById($newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($newsPublicationId) {
    $sqlStatement = "SELECT count(distinct nh1.id) as count FROM $this->tableName nh1, $this->tableName nh2 where nh1.id != nh2.id and nh1.news_publication_id = nh2.news_publication_id and nh1.list_order = nh2.list_order and nh1.news_publication_id = $newsPublicationId";
    return($this->querySelect($sqlStatement));
  }

}

?>
