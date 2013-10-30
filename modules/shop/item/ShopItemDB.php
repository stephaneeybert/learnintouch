<?

class ShopItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ShopItemDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_SHOP_ITEM;

    $this->dao = new ShopItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopItem();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setShortDescription($row['short_description']);
      $object->setLongDescription($row['long_description']);
      $object->setReference($row['reference']);
      $object->setWeight($row['weight']);
      $object->setPrice($row['price']);
      $object->setVatRate($row['vat_rate']);
      $object->setShippingFee($row['shipping_fee']);
      $object->setCategoryId($row['category_id']);
      $object->setUrl($row['url']);
      $object->setListOrder($row['list_order']);
      $object->setHide($row['hide']);
      $object->setAdded($row['added']);
      $object->setLastModified($row['last_modified']);
      $object->setAvailable($row['available']);

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

  function selectByNextListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($categoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($categoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($categoryId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($categoryId) {
    if ($this->countDuplicateListOrderRows($categoryId) > 0) {
      if ($shopItems = $this->selectByCategoryIdOrderById($categoryId)) {
        if (count($shopItems) > 0) {
          $listOrder = 0;
          foreach ($shopItems as $shopItem) {
            $listOrder = $listOrder + 1;
            $shopItem->setListOrder($listOrder);
            $this->update($shopItem);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($categoryId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($categoryId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
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

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryId($categoryId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCategoryIdOrderById($categoryId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdOrderById($categoryId, $start, $rows)) {
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

    return($this->dao->insert($object->getName(), $object->getShortDescription(), $object->getLongDescription(), $object->getReference(), $object->getWeight(), $object->getPrice(), $object->getVatRate(), $object->getShippingFee(), $object->getCategoryId(), $object->getUrl(), $object->getListOrder(), $object->getHide(), $object->getAdded(), $object->getLastModified(), $object->getAvailable()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getShortDescription(), $object->getLongDescription(), $object->getReference(), $object->getWeight(), $object->getPrice(), $object->getVatRate(), $object->getShippingFee(), $object->getCategoryId(), $object->getUrl(), $object->getListOrder(), $object->getHide(), $object->getAdded(), $object->getLastModified(), $object->getAvailable()));
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
