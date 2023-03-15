<?php

class NavbarItemDao extends Dao {

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
name varchar(255),
image varchar(255),
image_over varchar(255),
url varchar(255),
blank_target boolean not null,
description varchar(255),
hide boolean not null,
template_model_id int unsigned,
index (template_model_id), foreign key (template_model_id) references template_model(id),
list_order int unsigned not null,
navbar_language_id int unsigned not null,
index (navbar_language_id), foreign key (navbar_language_id) references navbar_language(id),
index (navbar_language_id, list_order),
index (image),
index (image_over),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $image, $imageOver, $url, $blankTarget, $description, $hide, $templateModelId, $listOrder, $navbarLanguageId) {
    $templateModelId = LibString::emptyToNULL($templateModelId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$image', '$imageOver', '$url', '$blankTarget', '$description', '$hide', $templateModelId, '$listOrder', '$navbarLanguageId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $image, $imageOver, $url, $blankTarget, $description, $hide, $templateModelId, $listOrder, $navbarLanguageId) {
    $templateModelId = LibString::emptyToNULL($templateModelId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', image = '$image', image_over = '$imageOver', url = '$url', blank_target = '$blankTarget', description = '$description', hide = '$hide', template_model_id = $templateModelId, list_order = '$listOrder', navbar_language_id = '$navbarLanguageId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function resetNavigationModelReferences($templateModelId) {
    $sqlStatement = "UPDATE $this->tableName SET template_model_id = NULL WHERE template_model_id = '$templateModelId'";
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

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavbarLanguageId($navbarLanguageId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_language_id = '$navbarLanguageId' ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavbarLanguageIdOrderById($navbarLanguageId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_language_id = '$navbarLanguageId' ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($navbarLanguageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_language_id = '$navbarLanguageId' AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($navbarLanguageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_language_id = '$navbarLanguageId' AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($navbarLanguageId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navbar_language_id = '$navbarLanguageId' AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectByImageOver($imageOver) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image_over = '$imageOver'";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($navbarLanguageId) {
    $sqlStatement = "SELECT count(distinct ni1.id) as count FROM $this->tableName ni1, $this->tableName ni2 where ni1.id != ni2.id and ni1.navbar_language_id = ni2.navbar_language_id and ni1.list_order = ni2.list_order and ni1.navbar_language_id = $navbarLanguageId";
    return($this->querySelect($sqlStatement));
  }

}

?>
