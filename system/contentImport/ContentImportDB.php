<?

class ContentImportDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ContentImportDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_CONTENT_IMPORT;

    $this->dao = new ContentImportDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ContentImport();
      $object->setId($row['id']);
      $object->setDomainName($row['domain_name']);
      $object->setIsImporting($row['is_importing']);
      $object->setIsExporting($row['is_exporting']);
      $object->setPermissionKey($row['permission_key']);
      $object->setPermissionStatus($row['permission_status']);

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

  function selectByDomainName($domainName) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByDomainName($domainName)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByDomainNameAndIsImporting($domainName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByDomainNameAndIsImporting($domainName)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByDomainNameAndIsExporting($domainName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByDomainNameAndIsExporting($domainName)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectImporting($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectImporting($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectExporting($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectExporting($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->insert($object->getDomainName(), $object->getIsImporting(), $object->getIsExporting(), $object->getPermissionKey(), $object->getPermissionStatus()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getDomainName(), $object->getIsImporting(), $object->getIsExporting(), $object->getPermissionKey(), $object->getPermissionStatus()));
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
