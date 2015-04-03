<?php

class WebsiteAddressDao extends Dao {

  var $tableName;

  function WebsiteAddressDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
address1 varchar(50),
address2 varchar(50),
zip_code varchar(10),
city varchar(50),
state varchar(50),
country varchar(50),
postal_box varchar(50),
telephone varchar(20),
mobile varchar(20),
fax varchar(20),
vat_number varchar(50),
website_id int unsigned not null,
index (website_id), foreign key (website_id) references website(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($address1, $address2, $zipCode, $city, $state, $country, $postalBox, $telephone, $mobile, $fax, $vatNumber, $websiteId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$address1', '$address2', '$zipCode', '$city', '$state', '$country', '$postalBox', '$telephone', '$mobile', '$fax', '$vatNumber', '$websiteId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $address1, $address2, $zipCode, $city, $state, $country, $postalBox, $telephone, $mobile, $fax, $vatNumber, $websiteId) {
    $sqlStatement = "UPDATE $this->tableName SET address1 = '$address1', address2 = '$address2', zip_code = '$zipCode', city = '$city', state = '$state', country = '$country', postal_box = '$postalBox', telephone = '$telephone', mobile = '$mobile', fax = '$fax', vat_number = '$vatNumber', website_id = '$websiteId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY country, zip_code";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByWebsite($websiteId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE website_id = '$websiteId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
