<?php

class WebsiteDao extends Dao {

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
name varchar(255) not null,
system_name varchar(50) not null,
db_name varchar(50) not null,
domain_name varchar(255) not null,
firstname varchar(255),
lastname varchar(255),
email varchar(255),
disk_space int unsigned,
package varchar(50),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $systemName, $dbName, $domainName, $firstname, $lastname, $email, $diskSpace, $package) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$systemName', '$dbName', '$domainName', '$firstname', '$lastname', '$email', '$diskSpace', '$package')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $systemName, $dbName, $domainName, $firstname, $lastname, $email, $diskSpace, $package) {
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', system_name = '$systemName', db_name = '$dbName', domain_name = '$domainName', firstname = '$firstname', lastname = '$lastname', email = '$email', disk_space = '$diskSpace', package = '$package' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY name, firstname";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email'";
    return($this->querySelect($sqlStatement));
  }

  function selectBySystemName($systemName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE system_name = '$systemName'";
    return($this->querySelect($sqlStatement));
  }

  function selectByDbName($dbName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE db_name = '$dbName'";
    return($this->querySelect($sqlStatement));
  }

  function selectByDomainName($domainName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE domain_name = '$domainName'";
    return($this->querySelect($sqlStatement));
  }

}

?>
