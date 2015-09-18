<?php

class PhotoAlbumDao extends Dao {

  var $tableName;

  function PhotoAlbumDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50) not null,
folder_name varchar(50) not null,
event varchar(255),
location varchar(255),
publication_date datetime,
price int unsigned,
hide boolean not null,
no_slide_show boolean not null,
no_zoom boolean not null,
hide boolean not null,
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $folderName, $event, $location, $publicationDate, $price, $hide, $noSlideShow, $noZoom, $listOrder) {
    $publicationDate = LibString::emptyToNULL($publicationDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$folderName', '$event', '$location', $publicationDate, '$price', '$hide', '$noSlideShow', '$noZoom', '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $folderName, $event, $location, $publicationDate, $price, $hide, $noSlideShow, $noZoom, $listOrder) {
    $publicationDate = LibString::emptyToNULL($publicationDate);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', folder_name = '$folderName', event = '$event', location = '$location', publication_date = $publicationDate, price = '$price', hide = '$hide', no_slide_show = '$noSlideShow', no_zoom = '$noZoom', list_order = '$listOrder' WHERE id = '$id'";
error_log($sqlStatement);
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

  function selectAllOrderById() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectNotHidden() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE hide != '1' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByFolderName($folderName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE folder_name = '$folderName' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE lower(name) LIKE lower('%$searchPattern%') OR lower(event) LIKE lower('%$searchPattern%') OR lower(location) LIKE lower('%$searchPattern%') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows() {
    $sqlStatement = "SELECT count(distinct pa1.id) as count FROM $this->tableName pa1, $this->tableName pa2 where pa1.id != pa2.id and pa1.list_order = pa2.list_order";
    return($this->querySelect($sqlStatement));
  }

}

?>
