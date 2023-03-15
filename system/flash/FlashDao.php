<?php

class FlashDao extends Dao {

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
filename varchar(50),
width varchar(10),
height varchar(10),
bgcolor varchar(10),
wddx varchar(50),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($filename, $width, $height, $bgcolor, $wddx) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$filename', '$width', '$height', '$bgcolor', '$wddx')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $filename, $width, $height, $bgcolor, $wddx) {
    $sqlStatement = "UPDATE $this->tableName SET filename = '$filename', width = '$width', height = '$height', bgcolor = '$bgcolor', wddx = '$wddx' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY filename";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByFile($filename) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE filename = '$filename'";
    return($this->querySelect($sqlStatement));
  }

  function selectByWddxFile($wddx) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE wddx = '$wddx'";
    return($this->querySelect($sqlStatement));
  }

}

?>
