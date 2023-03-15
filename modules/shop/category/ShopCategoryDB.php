<?

class ShopCategoryDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_SHOP_CATEGORY;

    $this->dao = new ShopCategoryDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopCategory();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setListOrder($row['list_order']);
      $object->setParentCategoryId($row['parent_id']);

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

  function selectByNextListOrder($parentCategoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($parentCategoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($parentCategoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($parentCategoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($parentCategoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByListOrder($parentCategoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($parentCategoryId) {
    if ($this->countDuplicateListOrderRows($parentCategoryId) > 0) {
      if ($shopCategories = $this->selectByParentCategoryIdOrderById($parentCategoryId)) {
        if (count($shopCategories) > 0) {
          $listOrder = 0;
          foreach ($shopCategories as $shopCategory) {
            $listOrder = $listOrder + 1;
            $shopCategory->setListOrder($listOrder);
            $this->update($shopCategory);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($parentCategoryId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($parentCategoryId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByParentCategoryId($parentCategoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentCategoryId($parentCategoryId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByParentCategoryIdOrderById($parentCategoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentCategoryIdOrderById($parentCategoryId)) {
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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getListOrder(), $object->getParentCategoryId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getListOrder(), $object->getParentCategoryId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    // Delete all the sub categories of the category
    if ($shopCategories = $this->selectByParentCategoryId($id)) {
      foreach ($shopCategories as $shopCategory) {
        $shopCategoryId = $shopCategory->getId();
        $this->delete($shopCategoryId);
      }
    }

    return($this->dao->delete($id));
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

}

?>
