<?

class DocumentCategoryDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function DocumentCategoryDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_DOCUMENT_CATEGORY;

    $this->dao = new DocumentCategoryDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new DocumentCategory();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setListOrder($row['list_order']);

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

  function selectByNextListOrder($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder() {
    if ($this->countDuplicateListOrderRows() > 0) {
      if ($documentCategories = $this->selectAllOrderById()) {
        if (count($documentCategories) > 0) {
          $listOrder = 0;
          foreach ($documentCategories as $documentCategory) {
            $listOrder = $listOrder + 1;
            $documentCategory->setListOrder($listOrder);
            $this->update($documentCategory);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows();

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

  function selectAllOrderById() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllOrderById()) {
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getListOrder()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

}

?>
