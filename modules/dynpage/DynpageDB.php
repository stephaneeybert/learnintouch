<?

class DynpageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function DynpageDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_DYNPAGE;

    $this->dao = new DynpageDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Dynpage();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setContent($row['content']);
      $object->setHide($row['hide']);
      $object->setGarbage($row['garbage']);
      $object->setListOrder($row['list_order']);
      $object->setSecured($row['secured']);
      $object->setParentId($row['parent_id']);
      $object->setAdminId($row['admin_id']);

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

  function selectByParentId($parentId = '') {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentId($parentId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByParentIdOrderById($parentId = '') {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentIdOrderById($parentId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByParentIdAndName($parentId, $name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByParentIdAndName($parentId, $name)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByParentIdAndNameAndNotGarbage($parentId, $name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByParentIdAndNameAndNotGarbage($parentId, $name)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByNextListOrder($parentId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($parentId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($parentId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($parentId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($parentId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($parentId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($parentId) {
    if ($this->countDuplicateListOrderRows($parentId) > 0) {
      if ($dynpages = $this->selectByParentIdOrderById($parentId)) {
        if (count($dynpages) > 0) {
          $listOrder = 0;
          foreach ($dynpages as $dynpage) {
            $listOrder = $listOrder + 1;
            $dynpage->setListOrder($listOrder);
            $this->update($dynpage);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($parentId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($parentId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
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

  function selectGarbage() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectGarbage()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNonGarbage() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNonGarbage()) {
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

  function selectByAdminId($adminId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAdminId($adminId)) {
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

    $content = $object->getContent();
    $content = LibString::databaseEscapeQuotes($content);

    return($this->dao->insert($object->getName(), $object->getDescription(), $content, $object->getHide(), $object->getGarbage(), $object->getListorder(), $object->getSecured(), $object->getParentId(), $object->getAdminId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $content = $object->getContent();
    $content = LibString::databaseEscapeQuotes($content);

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $content, $object->getHide(), $object->getGarbage(), $object->getListorder(), $object->getSecured(), $object->getParentId(), $object->getAdminId()));
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
