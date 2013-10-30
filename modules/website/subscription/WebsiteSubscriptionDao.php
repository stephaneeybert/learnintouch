<?php

class WebsiteSubscriptionDao extends Dao {

  var $tableName;

  function WebsiteSubscriptionDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
opening_date datetime not null,
fee int unsigned,
duration int unsigned,
auto_renewal boolean not null,
termination_date datetime,
website_id int unsigned not null,
index (website_id), foreign key (website_id) references website(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($openingDate, $fee, $duration, $autoRenewal, $terminationDate, $websiteId) {
    $openingDate = LibString::emptyToNULL($openingDate);
    $terminationDate = LibString::emptyToNULL($terminationDate);
    $openingDate = LibString::addSingleQuotesIfNotNULL($openingDate);
    $terminationDate = LibString::addSingleQuotesIfNotNULL($terminationDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', $openingDate, '$fee', '$duration', '$autoRenewal', $terminationDate, '$websiteId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $openingDate, $fee, $duration, $autoRenewal, $terminationDate, $websiteId) {
    $openingDate = LibString::emptyToNULL($openingDate);
    $terminationDate = LibString::emptyToNULL($terminationDate);
    $openingDate = LibString::addSingleQuotesIfNotNULL($openingDate);
    $terminationDate = LibString::addSingleQuotesIfNotNULL($terminationDate);
    $sqlStatement = "UPDATE $this->tableName SET opening_date = $openingDate, fee = '$fee', duration = '$duration', auto_renewal = '$autoRenewal', termination_date = $terminationDate, website_id = '$websiteId' WHERE id = '$id'";
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

}

?>
