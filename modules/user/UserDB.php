<?

class UserDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function UserDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_USER;

    $this->dao = new UserDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new User();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setOrganisation($row['organisation']);
      $object->setEmail($row['email']);
      $object->setHomePhone($row['home_phone']);
      $object->setWorkPhone($row['work_phone']);
      $object->setFax($row['fax']);
      $object->setMobilePhone($row['mobile_phone']);
      $object->setPassword($row['password']);
      $object->setPasswordSalt($row['password_salt']);
      $object->setReadablePassword($row['readable_password']);
      $object->setUnconfirmedEmail($row['unconfirmed_email']);
      $object->setValidUntil($row['valid_until']);
      $object->setLastLogin($row['last_login']);
      $object->setProfile($row['profile']);
      $object->setImage($row['image']);
      $object->setImported($row['imported']);
      $object->setMailSubscribe($row['mail_subscribe']);
      $object->setSmsSubscribe($row['sms_subscribe']);
      $object->setCreationDateTime($row['creation_datetime']);
      $object->setAddressId($row['address_id']);

      return($object);
    }
  }

  function selectById($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByEmail($email) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByEmail($email)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByMobilePhone($mobilePhone) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByMobilePhone($mobilePhone)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByEmailAndPassword($email, $password) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByEmailAndPassword($email, $password)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByEmailAndReadablePassword($email, $readablePassword) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByEmailAndReadablePassword($email, $readablePassword)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectAll($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAll($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function searchMailSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->searchMailSubscribersLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function searchNotMailSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->searchNotMailSubscribersLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function searchSmsSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->searchSmsSubscribersLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function searchNotSmsSubscribersLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->searchNotSmsSubscribersLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function searchMailSubscribersLikeCountry($searchCountry, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->searchMailSubscribersLikeCountry($searchCountry, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectImported() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectImported()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function countAll() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countAll();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function countImported() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countImported();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function countFoundRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countFoundRows();
    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectAllMailSubscribers() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllMailSubscribers()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectExpiredMailSubscribers($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectExpiredMailSubscribers($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectCurrentMailSubscribers($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectCurrentMailSubscribers($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNotValid($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotValid($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectValidTemporarily($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectValidTemporarily($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectValidPermanently($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectValidPermanently($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function countNotValidPermanently() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countNotValidPermanently();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectLoggedInSince($lastLoginSinceDate, $lastLoginUntilDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLoggedInSince($lastLoginSinceDate, $lastLoginUntilDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectCurrentSmsSubscribers($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectCurrentSmsSubscribers($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectExpiredSmsSubscribers($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectExpiredSmsSubscribers($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAllSmsSubscribers() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllSmsSubscribers()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNotYetConfirmedEmail($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotYetConfirmedEmail($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByMailListId($mailListId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByMailListId($mailListId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCreationDateTime($fromDate, $toDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCreationDateTime($fromDate, $toDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getOrganisation(), $object->getEmail(), $object->getHomePhone(), $object->getWorkPhone(), $object->getFax(), $object->getMobilePhone(), $object->getPassword(), $object->getPasswordSalt(), $object->getReadablePassword(), $object->getUnconfirmedEmail(), $object->getValidUntil(), $object->getLastLogin(), $object->getProfile(), $object->getImage(), $object->getImported(), $object->getMailSubscribe(), $object->getSmsSubscribe(), $object->getCreationDateTime(), $object->getAddressId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(), $object->getOrganisation(), $object->getEmail(), $object->getHomePhone(), $object->getWorkPhone(), $object->getFax(), $object->getMobilePhone(), $object->getReadablePassword(), $object->getUnconfirmedEmail(), $object->getValidUntil(), $object->getLastLogin(), $object->getProfile(), $object->getImage(), $object->getImported(), $object->getMailSubscribe(), $object->getSmsSubscribe(), $object->getCreationDateTime(), $object->getAddressId()));
  }

  function updatePassword($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->updatePassword($object->getId(), $object->getPassword(), $object->getPasswordSalt(), $object->getReadablePassword()));
  }

  function resetImported() {
    $this->dataSource->selectDatabase();

    return($this->dao->resetImported());
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
