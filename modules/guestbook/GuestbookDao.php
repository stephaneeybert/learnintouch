<?php

class GuestbookDao extends Dao {

  var $tableName;

  function GuestbookDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
body text not null,
user_account_id int unsigned,
index (user_account_id), foreign key (user_account_id) references user_account(id),
email varchar(255),
firstname varchar(255),
lastname varchar(255),
publication_datetime datetime not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($body, $releaseDate, $userId, $email, $firstname, $lastname) {
    $userId = LibString::emptyToNULL($userId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$body', $userId, '$email', '$firstname', '$lastname', '$releaseDate')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $body, $releaseDate, $userId, $email, $firstname, $lastname) {
    $userId = LibString::emptyToNULL($userId);
    $sqlStatement = "UPDATE $this->tableName SET body = '$body', publication_datetime = '$releaseDate', user_account_id = $userId, email = '$email', firstname = '$firstname', lastname = '$lastname' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY publication_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' OR (user_account_id IS NULL AND '$userId' < '1')";
    return($this->querySelect($sqlStatement));
  }

}

?>
