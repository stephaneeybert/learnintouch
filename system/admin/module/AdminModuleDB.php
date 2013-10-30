<?

class AdminModuleDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function AdminModuleDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ADMIN_MODULE;

    $this->dao = new AdminModuleDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new AdminModule();
      $object->setId($row['id']);
      $object->setModule($row['module']);
      $object->setAdmin($row['admin_id']);

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

  function selectByModuleAndAdmin($module, $adminId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByModuleAndAdmin($module, $adminId)) {
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

  function selectByModule($module) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByModule($module)) {
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

    return($this->dao->insert($object->getModule(), $object->getAdmin()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getModule(), $object->getAdmin()));
    }

  // Delete all the modules for an administrator
  function deleteAdminModules($adminId) {
    $adminModules = $this->selectByAdmin($adminId);
    foreach ($adminModules as $adminModule) {
      $this->delete($adminModule->getId());
      }
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
