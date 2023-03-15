<?php

class NavlinkItemDao extends Dao {

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
description varchar(255),
image varchar(255),
image_over varchar(255),
url varchar(255),
blank_target boolean not null,
language_code varchar(2),
template_model_id int unsigned,
index (template_model_id), foreign key (template_model_id) references template_model(id),
navlink_id int unsigned not null,
index (navlink_id), foreign key (navlink_id) references navlink(id),
index (navlink_id, language_code),
index (image),
index (image_over),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $image, $imageOver, $url, $blankTarget, $language, $templateModelId, $navlinkId) {
    $templateModelId = LibString::emptyToNULL($templateModelId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$image', '$imageOver', '$url', '$blankTarget', '$language', $templateModelId, '$navlinkId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $image, $imageOver, $url, $blankTarget, $language, $templateModelId, $navlinkId) {
    $templateModelId = LibString::emptyToNULL($templateModelId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', image = '$image', image_over = '$imageOver', url = '$url', blank_target = '$blankTarget', language_code = '$language', template_model_id = $templateModelId, navlink_id = '$navlinkId' WHERE id = '$id'";
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

  function selectByNoLanguageAndNavlinkId($navlinkId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (language_code = '0' OR language_code = '') AND navlink_id = '$navlinkId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByLanguageAndNavlinkId($language, $navlinkId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navlink_id = '$navlinkId' && language_code = '$language' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNavlinkId($navlinkId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE navlink_id = '$navlinkId'";
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

}

?>
