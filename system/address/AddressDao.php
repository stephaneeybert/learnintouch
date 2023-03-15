<?php

class AddressDao extends Dao {

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
address1 varchar(255),
address2 varchar(255),
zip_code varchar(10),
city varchar(255),
state varchar(255),
country varchar(255),
postal_box varchar(50),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($address1, $address2, $zipCode, $city, $state, $country, $postalBox) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$address1', '$address2', '$zipCode', '$city', '$state', '$country', '$postalBox')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $address1, $address2, $zipCode, $city, $state, $country, $postalBox) {
    $sqlStatement = "UPDATE $this->tableName SET address1 = '$address1', address2 = '$address2', zip_code = '$zipCode', city = '$city', state = '$state', country = '$country', postal_box = '$postalBox' WHERE id = '$id'";
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

}

?>
