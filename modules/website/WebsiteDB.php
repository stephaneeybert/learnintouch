<?

class WebsiteDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function WebsiteDB() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->tableName = DB_TABLE_WEBSITE;

    $this->dao = new WebsiteDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Website();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setSystemName($row['system_name']);
      $object->setDbName($row['db_name']);
      $object->setDomainName($row['domain_name']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setEmail($row['email']);
      $object->setDiskSpace($row['disk_space']);
      $object->setPackage($row['package']);

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

  function selectBySystemName($systemName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectBySystemName($systemName)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByDbName($dbName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByDbName($dbName)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByDomainName($domainName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByDomainName($domainName)) {
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

  function selectAll() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAll()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Insert the object
  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getName(), $object->getSystemName(), $object->getDbName(), $object->getDomainName(), $object->getFirstname(), $object->getLastname(), $object->getEmail(), $object->getDiskSpace(), $object->getPackage()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getSystemName(), $object->getDbName(), $object->getDomainName(), $object->getFirstname(), $object->getLastname(), $object->getEmail(), $object->getDiskSpace(), $object->getPackage()));
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
