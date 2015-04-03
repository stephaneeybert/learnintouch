<?php

class UserDao extends Dao {

  var $tableName;

  function UserDao($dataSource, $tableName) {
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
organisation varchar(255),
email varchar(255) not null,
unique (email),
fax varchar(20),
home_phone varchar(20),
work_phone varchar(20),
mobile_phone varchar(20),
password varchar(100) not null,
password_salt varchar(50) not null,
readable_password varchar(50),
unconfirmed_email boolean not null,
valid_until datetime,
last_login datetime not null,
profile text,
image varchar(255),
imported boolean not null,
mail_subscribe boolean not null,
sms_subscribe boolean not null,
creation_datetime datetime not null,
address_id int unsigned,
index (address_id), foreign key (address_id) references address(id),
index (image),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $organisation, $email, $fax, $homePhone, $workPhone, $mobilePhone, $password, $passwordSalt, $readablePassword, $unconfirmedEmail, $validUntil, $lastLogin, $profile, $image, $imported, $mailSubscribe, $smsSubscribe, $creationDateTime, $addressId) {
    $addressId = LibString::emptyToNULL($addressId);
    $validUntil = LibString::emptyToNULL($validUntil);
    $validUntil = LibString::addSingleQuotesIfNotNULL($validUntil);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$organisation', '$email', '$fax', '$homePhone', '$workPhone', '$mobilePhone', '$password', '$passwordSalt', '$readablePassword', '$unconfirmedEmail', $validUntil, '$lastLogin', '$profile', '$image', '$imported', '$mailSubscribe', '$smsSubscribe', '$creationDateTime', $addressId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $organisation, $email, $homePhone, $workPhone, $fax, $mobilePhone, $readablePassword, $unconfirmedEmail, $validUntil, $lastLogin, $profile, $image, $imported, $mailSubscribe, $smsSubscribe, $creationDateTime, $addressId) {
    $addressId = LibString::emptyToNULL($addressId);
    $validUntil = LibString::emptyToNULL($validUntil);
    $validUntil = LibString::addSingleQuotesIfNotNULL($validUntil);
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', organisation = '$organisation', email = '$email', home_phone = '$homePhone', work_phone = '$workPhone', fax = '$fax', mobile_phone = '$mobilePhone', readable_password = '$readablePassword', unconfirmed_email = '$unconfirmedEmail', valid_until = $validUntil, last_login = '$lastLogin', profile = '$profile', image = '$image', imported = '$imported', mail_subscribe = '$mailSubscribe', sms_subscribe = '$smsSubscribe', address_id = $addressId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function updatePassword($id, $password, $passwordSalt, $readablePassword) {
    $sqlStatement = "UPDATE $this->tableName SET password = '$password', password_salt = '$passwordSalt', readable_password = '$readablePassword' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
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

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByMobilePhone($mobilePhone) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mobile_phone = '$mobilePhone' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    if (strstr($searchPattern, ' ')) {
      list($firstname, $lastname) = explode(' ', $searchPattern);
      $OR_BOTH_NAMES = "OR (lower(firstname) LIKE lower('%$firstname%') AND lower(lastname) LIKE lower('%$lastname%'))";
    } else {
      $OR_BOTH_NAMES = "";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(email) LIKE lower('%$searchPattern%') OR lower(organisation) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR home_phone LIKE '%$searchPattern%' OR work_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' $OR_BOTH_NAMES ORDER BY firstname, lastname";
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

  function searchMailSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    if (strstr($searchPattern, ' ')) {
      list($firstname, $lastname) = explode(' ', $searchPattern);
      $OR_BOTH_NAMES = "OR (lower(firstname) LIKE lower('%$firstname%') AND lower(lastname) LIKE lower('%$lastname%'))";
    } else {
      $OR_BOTH_NAMES = "";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE mail_subscribe = '1' AND (lower(email) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR home_phone LIKE '%$searchPattern%' OR work_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' $OR_BOTH_NAMES) ORDER BY firstname, lastname";
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

  function searchNotMailSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    if (strstr($searchPattern, ' ')) {
      list($firstname, $lastname) = explode(' ', $searchPattern);
      $OR_BOTH_NAMES = "OR (lower(firstname) LIKE lower('%$firstname%') AND lower(lastname) LIKE lower('%$lastname%'))";
    } else {
      $OR_BOTH_NAMES = "";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE mail_subscribe != '1' AND (lower(email) LIKE lower('%$searchPattern%') OR lower(organisation) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR home_phone LIKE '%$searchPattern%' OR work_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' $OR_BOTH_NAMES) ORDER BY firstname, lastname";
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

  function searchSmsSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    if (strstr($searchPattern, ' ')) {
      list($firstname, $lastname) = explode(' ', $searchPattern);
      $OR_BOTH_NAMES = "OR (lower(firstname) LIKE lower('%$firstname%') AND lower(lastname) LIKE lower('%$lastname%'))";
    } else {
      $OR_BOTH_NAMES = "";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE sms_subscribe = '1' AND (lower(email) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR home_phone LIKE '%$searchPattern%' OR work_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' $OR_BOTH_NAMES) ORDER BY firstname, lastname";
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

  function searchNotSmsSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    if (strstr($searchPattern, ' ')) {
      list($firstname, $lastname) = explode(' ', $searchPattern);
      $OR_BOTH_NAMES = "OR (lower(firstname) LIKE lower('%$firstname%') AND lower(lastname) LIKE lower('%$lastname%'))";
    } else {
      $OR_BOTH_NAMES = "";
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE sms_subscribe != '1' AND (lower(email) LIKE lower('%$searchPattern%') OR lower(organisation) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR home_phone LIKE '%$searchPattern%' OR work_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' $OR_BOTH_NAMES) ORDER BY firstname, lastname";
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

  function searchMailSubscribersLikeCountry($searchCountry, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS u.* FROM $this->tableName u, " . DB_TABLE_ADDRESS . " a WHERE u.mail_subscribe = '1' AND lower(a.country) LIKE lower('%$searchCountry%') and u.address_id = a.id order by a.country, u.firstname, u.lastname";
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

  function selectByEmailAndPassword($email, $password) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' AND password = '$password'";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmailAndReadablePassword($email, $readablePassword) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' AND readable_password = '$readablePassword'";
    return($this->querySelect($sqlStatement));
  }

  function selectByMailListId($mailListId) {
    $sqlStatement = "SELECT u.* FROM $this->tableName u, " . DB_TABLE_MAIL_LIST_USER . " m WHERE m.mail_list_id = '$mailListId' and u.id = m.user_account_id order by u.lastname, u.firstname";
    return($this->querySelect($sqlStatement));
  }

  function selectAllMailSubscribers() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE mail_subscribe = '1' ORDER BY firstname, lastname, email";
    return($this->querySelect($sqlStatement));
  }

  function selectLoggedInSince($lastLoginSinceDate, $lastLoginUntilDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE last_login IS NOT NULL AND last_login >= '$lastLoginSinceDate' AND last_login < '$lastLoginUntilDate' ORDER BY last_login DESC";
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

  function selectExpiredSmsSubscribers($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE valid_until IS NOT NULL AND valid_until < '$systemDate' AND sms_subscribe = '1' AND mobile_phone != '' ORDER BY firstname, lastname, mobile_phone";
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

  function selectCurrentSmsSubscribers($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (valid_until IS NULL OR (valid_until IS NOT NULL AND valid_until >= '$systemDate')) AND sms_subscribe = '1' AND mobile_phone != '' ORDER BY firstname, lastname, mobile_phone";
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

  function selectAllSmsSubscribers() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE sms_subscribe = '1' AND mobile_phone != '' ORDER BY firstname, lastname, mobile_phone";
    return($this->querySelect($sqlStatement));
  }

  function selectNotValid($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE valid_until IS NOT NULL AND valid_until < '$systemDate' ORDER BY firstname, lastname";
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

  function selectExpiredMailSubscribers($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE valid_until IS NOT NULL AND valid_until < '$systemDate' AND mail_subscribe = '1' ORDER BY firstname, lastname";
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

  function selectValidTemporarily($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE valid_until IS NOT NULL AND valid_until >= '$systemDate' ORDER BY firstname, lastname";
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

  function selectValidPermanently($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE valid_until IS NULL ORDER BY firstname, lastname";
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

  function selectNotYetConfirmedEmail($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE unconfirmed_email = '1' ORDER BY firstname, lastname, mobile_phone";
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

  function selectCurrentMailSubscribers($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (valid_until IS NULL OR (valid_until IS NOT NULL AND valid_until >= '$systemDate')) AND mail_subscribe = '1' ORDER BY firstname, lastname";
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

  function countNotValidPermanently() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName WHERE valid_until IS NOT NULL";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectImported() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE imported = '1' ORDER BY firstname, lastname";
    return($this->querySelect($sqlStatement));
  }

  function selectWithReadablePassword() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE readable_password != '' ORDER BY firstname, lastname";
    return($this->querySelect($sqlStatement));
  }

  function countImported() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function resetImported() {
    $sqlStatement = "UPDATE $this->tableName SET imported != '1' WHERE imported = '1'";
    return($this->querySelect($sqlStatement));
  }

  function selectByCreationDateTime($fromDate, $toDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE DATE(creation_datetime) >= '$fromDate' AND DATE(creation_datetime) <= '$toDate' ORDER BY firstname, lastname";
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
