<?php

class MailListUserDao extends Dao {

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
mail_list_id int unsigned,
index (mail_list_id), foreign key (mail_list_id) references mail_list(id),
user_account_id int unsigned,
index (user_account_id), foreign key (user_account_id) references user(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($mailListId, $userId) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$mailListId', '$userId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $mailListId, $userId) {
    $sqlStatement = "UPDATE $this->tableName SET mail_list_id = '$mailListId', user_account_id = '$userId' WHERE id = '$id'";
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
    $sqlStatement = "SELECT m.* FROM $this->tableName m, " . DB_TABLE_USER . " u WHERE mail_list_id = '$mailListId' and u.id = m.user_account_id order by u.lastname, u.firstname";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListIdAndMailSubscribersLikePattern($mailListId, $searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(u.email) LIKE lower('%$bit%') OR lower(u.firstname) LIKE lower('%$bit%') OR lower(u.lastname) LIKE lower('%$bit%') OR u.home_phone LIKE '%$bit%' OR u.work_phone LIKE '%$bit%' OR u.fax LIKE '%$bit%' OR u.mobile_phone LIKE '%$bit%'";
        }
      }
    } else {
      $OR_CLAUSE = "lower(u.email) LIKE lower('%$searchPattern%') OR lower(u.firstname) LIKE lower('%$searchPattern%') OR lower(u.lastname) LIKE lower('%$searchPattern%') OR u.home_phone LIKE '%$searchPattern%' OR u.work_phone LIKE '%$searchPattern%' OR u.fax LIKE '%$searchPattern%' OR u.mobile_phone LIKE '%$searchPattern%'";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS m.* FROM $this->tableName m, " . DB_TABLE_USER . " u WHERE mail_list_id = '$mailListId' AND u.id = m.user_account_id AND u.mail_subscribe = '1' AND ($OR_CLAUSE) ORDER BY u.lastname, u.firstname";
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

  function selectByMailListIdAndMailSubscribersLikeCountry($mailListId, $searchCountry, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS m.* FROM $this->tableName m, " . DB_TABLE_USER . " u, " . DB_TABLE_ADDRESS . " a WHERE mail_list_id = '$mailListId' AND u.id = m.user_account_id AND u.mail_subscribe = '1' AND lower(a.country) LIKE lower('%$searchCountry%') AND u.address_id = a.id ORDER BY u.lastname, u.firstname";
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

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId'";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListIdAndUserId($mailListId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mail_list_id = '$mailListId' AND user_account_id = '$userId'";
    return($this->querySelect($sqlStatement));
  }

}

?>
