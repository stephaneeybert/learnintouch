<?php

class PeopleDao extends Dao {

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
firstname varchar(255) not null,
lastname varchar(255) not null,
email varchar(255),
work_phone varchar(20),
mobile_phone varchar(20),
profile text,
image varchar(255),
category_id int unsigned,
index (category_id), foreign key (category_id) references people_category(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $email, $workPhone, $mobilePhone, $profile, $image, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$email', '$workPhone', '$mobilePhone', '$profile', '$image', $categoryId, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $email, $workPhone, $mobilePhone, $profile, $image, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', email = '$email', work_phone = '$workPhone', mobile_phone = '$mobilePhone', profile = '$profile', image = '$image', category_id = $categoryId, list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY category_id, list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryId($categoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryIdOrderById($categoryId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($categoryId) {
    $sqlStatement = "SELECT count(distinct c1.id) as count FROM $this->tableName c1, $this->tableName c2 where c1.id != c2.id and c1.category_id = c2.category_id and c1.list_order = c2.list_order and c1.category_id = $categoryId";
    return($this->querySelect($sqlStatement));
  }

}

?>
