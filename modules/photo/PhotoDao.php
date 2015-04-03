<?php

class PhotoDao extends Dao {

  var $tableName;

  function PhotoDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
reference varchar(50),
name varchar(50),
description varchar(255),
tags varchar(255),
text_comment text,
image varchar(255),
url varchar(255),
price int unsigned,
photo_album_id int unsigned,
index (photo_album_id), foreign key (photo_album_id) references photo_album(id),
photo_format_id int unsigned,
index (photo_format_id), foreign key (photo_format_id) references photo_format(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($reference, $name, $description, $tags, $comment, $image, $url, $price, $photoAlbumId, $photoFormatId, $listOrder) {
    $photoAlbumId = LibString::emptyToNULL($photoAlbumId);
    $photoFormatId = LibString::emptyToNULL($photoFormatId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$reference', '$name', '$description', '$tags', '$comment', '$image', '$url', '$price', $photoAlbumId, $photoFormatId, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $reference, $name, $description, $tags, $comment, $image, $url, $price, $photoAlbumId, $photoFormatId, $listOrder) {
    $photoAlbumId = LibString::emptyToNULL($photoAlbumId);
    $photoFormatId = LibString::emptyToNULL($photoFormatId);
    $sqlStatement = "UPDATE $this->tableName SET reference = '$reference', name = '$name', description = '$description', tags = '$tags', text_comment = '$comment', image = '$image', url = '$url', price = '$price', photo_album_id = $photoAlbumId, photo_format_id = $photoFormatId, list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY photo_album_id, list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPhotoAlbum($photoAlbumId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE photo_album_id = '$photoAlbumId' OR (photo_album_id IS NULL AND '$photoAlbumId' < '1') ORDER BY list_order";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByPhotoAlbumOrderById($photoAlbumId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE photo_album_id = '$photoAlbumId' OR (photo_album_id IS NULL AND '$photoAlbumId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByReference($reference) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE reference = '$reference' ORDER BY photo_album_id, list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByFormat($photoFormatId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE photo_format_id = '$photoFormatId' OR (photo_format_id IS NULL AND '$photoFormatId' < '1') ORDER BY photo_album_id, list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($photoAlbumId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (photo_album_id = '$photoAlbumId' OR (photo_album_id IS NULL AND '$photoAlbumId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($photoAlbumId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (photo_album_id = '$photoAlbumId' OR (photo_album_id IS NULL AND '$photoAlbumId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($photoAlbumId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (photo_album_id = '$photoAlbumId' OR (photo_album_id IS NULL AND '$photoAlbumId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(reference) LIKE lower('%$searchPattern%') OR lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') OR lower(tags) LIKE lower('%$searchPattern%') OR lower(text_comment) LIKE lower('%$searchPattern%') OR lower(price) LIKE lower('%$searchPattern%') OR lower(image) LIKE lower('%$searchPattern%') ORDER BY photo_album_id, list_order";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByAlbumAndImage($album, $image) {
    $sqlStatement = "SELECT p.* FROM $this->tableName p, " . DB_TABLE_PHOTO_ALBUM . " pa WHERE p.photo_album_id = pa.id AND p.image = '$image' AND pa.name = '$album'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($photoAlbumId) {
    $sqlStatement = "SELECT count(distinct p1.id) as count FROM $this->tableName p1, $this->tableName p2 where p1.id != p2.id and p1.photo_album_id = p2.photo_album_id and p1.list_order = p2.list_order and p1.photo_album_id = $photoAlbumId";
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

}

?>
