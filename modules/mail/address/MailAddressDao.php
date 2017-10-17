<?php

class MailAddressDao extends Dao {

  var $tableName;

  function MailAddressDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
firstname varchar(255),
lastname varchar(255),
email varchar(100) not null,
unique (email),
text_comment text,
country varchar(255),
subscribe boolean not null,
imported boolean not null,
creation_datetime datetime,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $email, $comment, $country, $subscribe, $imported, $creationDateTime) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$email', '$comment', '$country', '$subscribe', '$imported', '$creationDateTime')";
    return($this->querySelect($sqlStatement));
  }

  function resetImported() {
    $sqlStatement = "UPDATE $this->tableName SET imported != '1'";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $email, $comment, $country, $subscribe, $imported, $creationDateTime) {
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', email = '$email', text_comment = '$comment', country = '$country', subscribe = '$subscribe', imported = '$imported', creation_datetime = '$creationDateTime' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteImported() {
    $sqlStatement = "DELETE FROM $this->tableName WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countImported() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
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

  function selectImported() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE imported = '1' ORDER BY firstname, lastname, email";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY firstname, lastname, email";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectSubscribersLikeCountry($searchPattern) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE subscribe = '1' AND lower(country) LIKE lower('%$searchPattern%')";
    return($this->querySelect($sqlStatement));
  }

  function selectSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(email) LIKE lower('%$bit%') OR lower(firstname) LIKE lower('%$bit%') OR lower(lastname) LIKE lower('%$bit%') OR lower(text_comment) LIKE lower('%$bit%') OR lower(country) LIKE lower('%$sbit%')";
        }
      }
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscribe = '1' AND ($OR_CLAUSE)";
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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(email) LIKE lower('%$bit%') OR lower(firstname) LIKE lower('%$bit%') OR lower(lastname) LIKE lower('%$bit%') OR lower(text_comment) LIKE lower('%$bit%') OR lower(country) LIKE lower('%$sbit%')";
        }
      }
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE $OR_CLAUSE ORDER BY firstname, lastname, email";
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

  function selectLikeCountry($searchCountry) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE lower(country) LIKE lower('%$searchCountry%') ORDER BY firstname, lastname, email";
    return($this->querySelect($sqlStatement));
  }

  function selectSubscribers($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscribe = '1' ORDER BY firstname, lastname, email";
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

  function selectNonSubscribers($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscribe != '1' ORDER BY firstname, lastname, email";
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

  function selectByCreationDateTime($fromDate, $toDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE DATE(creation_datetime) >= '$fromDate' AND DATE(creation_datetime) <= '$toDate' ORDER BY firstname, lastname, email";
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

}

?>
