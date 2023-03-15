<?php

class FormDao extends Dao {

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
name varchar(50) not null,
description varchar(255),
title varchar(255),
image varchar(255),
email varchar(255),
instructions text,
acknowledge text,
webpage_id varchar(255),
mail_subject varchar(255),
mail_message text,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $title, $image, $email, $instructions, $acknowledge, $webpageId, $mailSubject, $mailMessage) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$title', '$image', '$email', '$instructions', '$acknowledge', '$webpageId', '$mailSubject', '$mailMessage')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $title, $image, $email, $instructions, $acknowledge, $webpageId, $mailSubject, $mailMessage) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', title = '$title', image = '$image', email = '$email', instructions = '$instructions', acknowledge = '$acknowledge', webpage_id = '$webpageId', mail_subject = '$mailSubject', mail_message = '$mailMessage' WHERE id = '$id'";
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

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

}

?>
