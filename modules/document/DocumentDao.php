<?php

class DocumentDao extends Dao {

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
reference varchar(50),
description varchar(255),
filename varchar(50),
hide boolean not null,
secured boolean not null,
category_id int unsigned,
index (category_id), foreign key (category_id) references document_category(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($reference, $description, $file, $hide, $secured, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$reference', '$description', '$file', '$hide', '$secured', $categoryId, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $reference, $description, $file, $hide, $secured, $categoryId, $listOrder) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "UPDATE $this->tableName SET reference = '$reference', description = '$description', filename = '$file', hide = '$hide', secured = '$secured', category_id = $categoryId, list_order = '$listOrder' WHERE id = '$id'";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
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

  function selectByFile($file) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE filename = '$file'";
    return($this->querySelect($sqlStatement));
  }

  function selectPublished() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE hide != '1'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($categoryId) {
    $sqlStatement = "SELECT count(distinct d1.id) as count FROM $this->tableName d1, $this->tableName d2 where d1.id != d2.id and d1.category_id = d2.category_id and d1.list_order = d2.list_order and d1.category_id = $categoryId";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(reference) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') OR lower(filename) LIKE lower('%$searchPattern%') ORDER BY category_id, list_order";
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

}

?>
