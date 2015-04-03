<?php

class LocationStateDao extends Dao {

  var $tableName;

  function LocationStateDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
code varchar(4) not null,
unique (code),
name varchar(50) not null,
upper_name varchar(50) not null,
region varchar(4) not null,
country varchar(4) not null,
index (country, region),
index (code),
index (name),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($code, $name, $upperName, $region, $country) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$code', '$name', '$upperName', '$region', '$country')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $code, $name, $upperName, $region, $country) {
    $sqlStatement = "UPDATE $this->tableName SET code = '$code', name = '$name', upper_name = '$upperName', region = '$region', country = '$country' WHERE id = '$id'";
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

  function selectByCountryAndRegion($country, $region) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE country = '$country' AND region = '$region'";
    return($this->querySelect($sqlStatement));
  }

  function selectByRegion($region) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE region = '$region'";
    return($this->querySelect($sqlStatement));
  }

  function selectByCountry($country) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE country = '$country'";
    return($this->querySelect($sqlStatement));
  }

}

?>
