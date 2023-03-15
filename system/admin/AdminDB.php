<?

class AdminDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ADMIN;

    $this->dao = new AdminDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Admin();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setLogin($row['login']);
      $object->setPassword($row['password']);
      $object->setPasswordSalt($row['password_salt']);
      $object->setSuperAdmin($row['super_admin']);
      $object->setPreferenceAdmin($row['preference_admin']);
      $object->setAddress($row['address']);
      $object->setZipCode($row['zip_code']);
      $object->setCity($row['city']);
      $object->setCountry($row['country']);
      $object->setEmail($row['email']);
      $object->setProfile($row['profile']);
      $object->setPostLoginUrl($row['post_login_url']);

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

  function selectByLogin($login) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLogin($login)) {
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

  function selectByLoginAndPassword($login, $password) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLoginAndPassword($login, $password)) {
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

  function selectAllNonSuperAdminAndLoggedOne($login, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllNonSuperAdminAndLoggedOne($login, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getLogin(), $object->getPassword(), $object->getPasswordSalt(), $object->getSuperAdmin(), $object->getPreferenceAdmin(), $object->getAddress(), $object->getZipCode(), $object->getCity(), $object->getCountry(), $object->getEmail(), $object->getProfile(), $object->getPostLoginUrl()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(), $object->getLogin(), $object->getSuperAdmin(), $object->getPreferenceAdmin(), $object->getAddress(), $object->getZipCode(), $object->getCity(), $object->getCountry(), $object->getEmail(), $object->getProfile(), $object->getPostLoginUrl()));
  }

  function updatePassword($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->updatePassword($object->getId(), $object->getPassword(), $object->getPasswordSalt()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  // Get the id of the last inserted object
  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
