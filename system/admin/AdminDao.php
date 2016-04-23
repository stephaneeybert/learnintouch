<?php

class AdminDao extends Dao {

  var $tableName;

  function AdminDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
firstname varchar(255) not null,
lastname varchar(255) not null,
login varchar(50) not null,
unique (login),
password varchar(100) not null,
password_salt varchar(50) not null,
super_admin boolean not null,
preference_admin boolean not null,
address varchar(255),
zip_code varchar(10),
city varchar(255),
country varchar(255),
email varchar(100),
profile text,
post_login_url varchar(255),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $login, $password, $passwordSalt, $superAdmin, $preferenceAdmin, $address, $zipCode, $city, $country, $email, $profile, $postLoginUrl) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$login', '$password', '$passwordSalt', '$superAdmin', '$preferenceAdmin', '$address', '$zipCode', '$city', '$country', '$email', '$profile', '$postLoginUrl')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $login, $superAdmin, $preferenceAdmin, $address, $zipCode, $city, $country, $email, $profile, $postLoginUrl) {
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', login = '$login', super_admin = '$superAdmin', preference_admin = '$preferenceAdmin', address = '$address', zip_code = '$zipCode', city = '$city', country = '$country', email = '$email', profile = '$profile', post_login_url = '$postLoginUrl' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function updatePassword($id, $password, $passwordSalt) {
    $sqlStatement = "UPDATE $this->tableName SET password = '$password', password_salt = '$passwordSalt' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
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

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY firstname, lastname";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR lower(login) LIKE lower('%$searchPattern%') OR lower(email) LIKE lower('%$searchPattern%') ORDER BY firstname, lastname";
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

  function selectByLogin($login) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE login = '$login'";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByLoginAndPassword($login, $password) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE login = '$login' AND password = '$password'";
    return($this->querySelect($sqlStatement));
  }

  function selectAllNonSuperAdminAndLoggedOne($login, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE super_admin != '1' OR login = '$login'";
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
