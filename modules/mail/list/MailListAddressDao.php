<?php

class MailListAddressDao extends Dao {

  var $tableName;

  function MailListAddressDao($dataSource, $tableName) {
    $this->Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
mail_list_id int unsigned not null,
index (mail_list_id), foreign key (mail_list_id) references mail_list(id),
mail_address_id int unsigned not null,
index (mail_address_id), foreign key (mail_address_id) references mail_address(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($mailListId, $mailAddressId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$mailListId', '$mailAddressId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $mailListId, $mailAddressId) {
    $sqlStatement = "UPDATE $this->tableName SET mail_list_id = '$mailListId', mail_address_id = '$mailAddressId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteByMailListId($mailListId) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE mail_list_id = '$mailListId'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListId($mailListId) {
    $sqlStatement = "SELECT m.* FROM $this->tableName m, " . DB_TABLE_MAIL_ADDRESS . " ma WHERE m.mail_list_id = '$mailListId' and ma.id = m.mail_address_id order by ma.email";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListIdAndSubscribersLikeCountry($mailListId, $searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS m.* FROM $this->tableName m, " . DB_TABLE_MAIL_ADDRESS . " ma WHERE m.mail_list_id = '$mailListId' AND ma.id = m.mail_address_id AND ma.subscribe = '1' AND lower(ma.country) LIKE lower('%$searchPattern%') ORDER BY ma.email";
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

  function selectByMailListIdAndSubscribersLikePattern($mailListId, $searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(ma.email) LIKE lower('%$bit%') OR lower(ma.firstname) LIKE lower('%$bit%') OR lower(ma.lastname) LIKE lower('%$bit%') OR lower(ma.text_comment) LIKE lower('%$bit%') OR lower(ma.country) LIKE lower('%$bit%')";
        }
      }
    } else {
      $OR_CLAUSE = "lower(ma.email) LIKE lower('%$searchPattern%') OR lower(ma.firstname) LIKE lower('%$searchPattern%') OR lower(ma.lastname) LIKE lower('%$searchPattern%') OR lower(ma.text_comment) LIKE lower('%$searchPattern%') OR lower(ma.country) LIKE lower('%$searchPattern%')";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS m.* FROM $this->tableName m, " . DB_TABLE_MAIL_ADDRESS . " ma WHERE m.mail_list_id = '$mailListId' AND ma.id = m.mail_address_id AND ma.subscribe = '1' AND ($OR_CLAUSE) order by ma.email";
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

  function selectByMailAddressId($mailAddressId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mail_address_id = '$mailAddressId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListIdAndMailAddressId($mailListId, $mailAddressId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mail_list_id = '$mailListId' AND mail_address_id = '$mailAddressId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
