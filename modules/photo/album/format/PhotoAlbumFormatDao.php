<?php

class PhotoAlbumFormatDao extends Dao {

  var $tableName;

  function PhotoAlbumFormatDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
photo_album_id int unsigned not null,
index (photo_album_id), foreign key (photo_album_id) references photo_album(id),
photo_format_id int unsigned not null,
index (photo_format_id), foreign key (photo_format_id) references photo_format(id),
price int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($photoAlbumId, $photoFormatId, $price) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$photoAlbumId', '$photoFormatId', '$price')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $photoAlbumId, $photoFormatId, $price) {
    $sqlStatement = "UPDATE $this->tableName SET photo_album_id = '$photoAlbumId', photo_format_id = '$photoFormatId', price = '$price' WHERE id = '$id'";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPhotoFormatIdAndPhotoAlbumId($photoFormatId, $photoAlbumId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE photo_format_id = '$photoFormatId' AND photo_album_id = '$photoAlbumId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPhotoFormatId($photoFormatId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE photo_format_id = '$photoFormatId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByPhotoAlbumId($photoAlbumId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE photo_album_id = '$photoAlbumId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
