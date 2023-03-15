<?php

class ContentImportDao extends Dao {

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
domain_name varchar(255) not null,
is_importing boolean not null,
is_exporting boolean not null,
permission_key varchar(10),
permission_status varchar(10),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($domainName, $isImporting, $isExporting, $permissionKey, $permissionStatus) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$domainName', '$isImporting', '$isExporting', '$permissionKey', '$permissionStatus')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $domainName, $isImporting, $isExporting, $permissionKey, $permissionStatus) {
    $sqlStatement = "UPDATE $this->tableName SET domain_name = '$domainName', is_importing = '$isImporting', is_exporting = '$isExporting', permission_key = '$permissionKey', permission_status = '$permissionStatus' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(domain_name) LIKE lower('%$searchPattern%') ORDER BY domain_name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY domain_name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectImporting() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE is_importing = '1' ORDER BY domain_name";
    return($this->querySelect($sqlStatement));
  }

  function selectExporting() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE is_exporting = '1' ORDER BY domain_name";
    return($this->querySelect($sqlStatement));
  }

  function selectByDomainName($domainName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE domain_name = '$domainName'";
    return($this->querySelect($sqlStatement));
  }

  function selectByDomainNameAndIsImporting($domainName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE domain_name = '$domainName' AND is_importing = '1' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByDomainNameAndIsExporting($domainName) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE domain_name = '$domainName' AND is_exporting = '1' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
