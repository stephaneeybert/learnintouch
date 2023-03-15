<?php

class LocationZipCodeDao extends Dao {

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
code varchar(10),
unique (code),
name varchar(50),
country varchar(4),
index (country, code),
index (name),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($code, $name, $country) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$code', '$name', '$country')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $code, $name, $country) {
    $sqlStatement = "UPDATE $this->tableName SET code = '$code', name = '$name', country = '$country' WHERE id = '$id'";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCode($code) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE code = '$code'";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name'";
    return($this->querySelect($sqlStatement));
  }

  function selectByCountry($country) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE country = '$country'";
    return($this->querySelect($sqlStatement));
  }

  function selectByCountryAndState($country, $code) {
    if (!$code) {
      $code = -1;
    }
    $sqlStatement = "SELECT * FROM $this->tableName WHERE country = '$country' AND code LIKE '$code%'";
    return($this->querySelect($sqlStatement));
  }

}

?>
