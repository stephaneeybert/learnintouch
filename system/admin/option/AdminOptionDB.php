<?

class AdminOptionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function AdminOptionDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ADMIN_OPTION;

    $this->dao = new AdminOptionDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new AdminOption();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setAdmin($row['admin_id']);
      $object->setValue($row['value']);

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

  function selectByNameAndAdmin($name, $adminId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNameAndAdmin($name, $adminId)) {
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

  function selectByAdmin($adminId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAdmin($adminId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByName($name)) {
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

    return($this->dao->insert($object->getName(), $object->getAdmin(), $object->getValue()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getName(), $object->getAdmin(), $object->getValue()));
    }

  // Delete all the names for an administrator
  function deleteAdminOptions($adminId) {
    $adminNames = $this->selectByAdmin($adminId);
    foreach ($adminNames as $adminName) {
      $this->delete($adminName->getId());
      }
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
