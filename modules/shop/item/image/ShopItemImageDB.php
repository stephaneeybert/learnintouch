<?

class ShopItemImageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ShopItemImageDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_SHOP_ITEM_IMAGE;

    $this->dao = new ShopItemImageDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopItemImage();
      $object->setId($row['id']);
      $object->setImage($row['image']);
      $object->setDescription($row['description']);
      $object->setListOrder($row['list_order']);
      $object->setShopItemId($row['shop_item_id']);

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

  function selectByNextListOrder($shopItemId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($shopItemId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPreviousListOrder($shopItemId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($shopItemId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByListOrder($shopItemId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($shopItemId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($shopItemId) {
    if ($this->countDuplicateListOrderRows($shopItemId) > 0) {
      if ($shopItemImages = $this->selectByShopItemIdOrderById($shopItemId)) {
        if (count($shopItemImages) > 0) {
          $listOrder = 0;
          foreach ($shopItemImages as $shopItemImage) {
            $listOrder = $listOrder + 1;
            $shopItemImage->setListOrder($listOrder);
            $this->update($shopItemImage);
            }
          }
        }
      }
    }

  function countDuplicateListOrderRows($shopItemId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($shopItemId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function selectByShopItemId($shopItemId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByShopItemId($shopItemId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByShopItemIdOrderById($shopItemId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByShopItemIdOrderById($shopItemId)) {
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

    return($this->dao->insert($object->getImage(), $object->getDescription(), $object->getListOrder(), $object->getShopItemId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getImage(), $object->getDescription(), $object->getListOrder(), $object->getShopItemId()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
