<?

class SocialUserDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function SocialUserDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_SOCIAL_USER;

    $this->dao = new SocialUserDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new SocialUser();
      $object->setId($row['id']);
      $object->setFacebookUserId($row['facebook_user_id']);
      $object->setLinkedinUserId($row['linkedin_user_id']);
      $object->setGoogleUserId($row['google_user_id']);
      $object->setUserId($row['user_account_id']);

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

  function selectByFacebookUserIdAndUserId($facebookUserId, $userId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByFacebookUserIdAndUserId($facebookUserId, $userId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByFacebookUserId($facebookUserId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByFacebookUserId($facebookUserId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByLinkedinUserIdAndUserId($linkedinUserId, $userId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLinkedinUserIdAndUserId($linkedinUserId, $userId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByLinkedinUserId($linkedinUserId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLinkedinUserId($linkedinUserId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByGoogleUserIdAndUserId($googleUserId, $userId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByGoogleUserIdAndUserId($googleUserId, $userId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByGoogleUserId($googleUserId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByGoogleUserId($googleUserId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByUserId($userId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByUserId($userId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getFacebookUserId(), $object->getLinkedinUserId(), $object->getGoogleUserId(), $object->getUserId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getFacebookUserId(), $object->getLinkedinUserId(), $object->getGoogleUserId(), $object->getUserId()));
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
