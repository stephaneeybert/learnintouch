<?php

class SocialUserDao extends Dao {

  var $tableName;

  function SocialUserDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
facebook_user_id varchar(12),
unique (facebook_user_id),
linkedin_user_id varchar(48),
unique (linkedin_user_id),
google_user_id varchar(48),
unique (google_user_id),
twitter_user_id varchar(48),
unique (twitter_user_id),
user_account_id int unsigned not null,
index (user_account_id), foreign key (user_account_id) references user(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($facebookUserId, $linkedinUserId, $googleUserId, $twitterUserId, $userId) {
    $facebookUserId = LibString::emptyToNULL($facebookUserId);
    $linkedinUserId = LibString::emptyToNULL($linkedinUserId);
    $googleUserId = LibString::emptyToNULL($googleUserId);
    $twitterUserId = LibString::emptyToNULL($twitterUserId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', $facebookUserId, $linkedinUserId, $googleUserId, $twitterUserId, '$userId')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $facebookUserId, $linkedinUserId, $googleUserId, $twitterUserId, $userId) {
    $facebookUserId = LibString::emptyToNULL($facebookUserId);
    $linkedinUserId = LibString::emptyToNULL($linkedinUserId);
    $googleUserId = LibString::emptyToNULL($googleUserId);
    $twitterUserId = LibString::emptyToNULL($twitterUserId);
    $sqlStatement = "UPDATE $this->tableName SET facebook_user_id = $facebookUserId, linkedin_user_id = $linkedinUserId, google_user_id = $googleUserId, twitter_user_id = $twitterUserId, user_account_id = '$userId' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
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

  function selectByFacebookUserIdAndUserId($facebookUserId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE facebook_user_id = '$facebookUserId' AND user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByFacebookUserId($facebookUserId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE facebook_user_id = '$facebookUserId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByLinkedinUserIdAndUserId($linkedinUserId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE linkedin_user_id = '$linkedinUserId' AND user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByLinkedinUserId($linkedinUserId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE linkedin_user_id = '$linkedinUserId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTwitterUserIdAndUserId($twitterUserId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE twitter_user_id = '$twitterUserId' AND user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTwitterUserId($twitterUserId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE twitter_user_id = '$twitterUserId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByGoogleUserIdAndUserId($googleUserId, $userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE google_user_id = '$googleUserId' AND user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByGoogleUserId($googleUserId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE google_user_id = '$googleUserId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
