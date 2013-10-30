<?php

class DynpageDao extends Dao {

  var $tableName;

  function DynpageDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50) not null,
description varchar(255),
content longtext,
hide boolean not null,
garbage boolean not null,
list_order int unsigned not null,
secured boolean not null,
parent_id int unsigned,
index (parent_id), foreign key (parent_id) references webpage(id),
admin_id int unsigned,
index (admin_id), foreign key (admin_id) references admin(id),
index (parent_id, name),
index (parent_id, list_order),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $content, $hide, $garbage, $listOrder, $secured, $parentId, $adminId) {
    $parentId = LibString::emptyToNULL($parentId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$content', '$hide', '$garbage', '$listOrder', '$secured', $parentId, $adminId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $content, $hide, $garbage, $listOrder, $secured, $parentId, $adminId) {
    $parentId = LibString::emptyToNULL($parentId);
    $adminId = LibString::emptyToNULL($adminId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', content = '$content', hide = '$hide', garbage = '$garbage', list_order = '$listOrder', secured = '$secured', parent_id = $parentId, admin_id = $adminId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY name";
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

  function selectByParentId($parentId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1') ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByParentIdOrderById($parentId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByParentIdAndName($parentId, $name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1')) AND name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByParentIdAndNameAndNotGarbage($parentId, $name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1')) AND name = '$name' AND garbage != '1' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($parentId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($parentId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($parentId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (parent_id = '$parentId' OR (parent_id IS NULL AND '$parentId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectGarbage() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE garbage = '1'";
    return($this->querySelect($sqlStatement));
  }

  function selectNonGarbage() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE garbage != '1'";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE content LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($parentId) {
    $sqlStatement = "SELECT count(distinct d1.id) as count FROM $this->tableName d1, $this->tableName d2 where d1.id != d2.id and d1.parent_id = d2.parent_id and d1.list_order = d2.list_order and d1.parent_id = '$parentId'";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%') OR lower(content) LIKE lower('%$searchPattern%')) OR id = '$searchPattern' ORDER BY name";
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

  function selectByAdminId($adminId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE admin_id = '$adminId' ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

}

?>
