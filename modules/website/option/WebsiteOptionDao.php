<?php

class WebsiteOptionDao extends Dao {

  var $tableName;

  function WebsiteOptionDao($dataSource, $tableName) {
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
value varchar(20),
website_id int unsigned not null,
index (website_id), foreign key (website_id) references website(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
    }

  function insert($name, $value, $websiteId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$value', '$websiteId')";
    return($this->querySelect($sqlStatement));
    }

  function update($id, $name, $value, $websiteId) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', value = '$value', website_id = '$websiteId' WHERE id = '$id'";
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

  function selectByWebsiteId($websiteId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE website_id = '$websiteId'";
    return($this->querySelect($sqlStatement));
    }

  function selectByNameAndWebsiteId($name, $websiteId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' AND website_id = '$websiteId' LIMIT 1";
    return($this->querySelect($sqlStatement));
    }

  }

?>
