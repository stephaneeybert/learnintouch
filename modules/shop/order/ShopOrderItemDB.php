<?

class ShopOrderItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ShopOrderItemDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_SHOP_ORDER_ITEM;

    $this->dao = new ShopOrderItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopOrderItem();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setShortDescription($row['short_description']);
      $object->setReference($row['reference']);
      $object->setPrice($row['price']);
      $object->setVatRate($row['vat_rate']);
      $object->setShippingFee($row['shipping_fee']);
      $object->setQuantity($row['quantity']);
      $object->setIsGift($row['is_gift']);
      $object->setOptions($row['options']);
      $object->setShopOrderId($row['shop_order_id']);
      $object->setShopItemId($row['shop_item_id']);
      $object->setImageUrl($row['image_url']);

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

  function selectByShopOrderId($shopOrderId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByShopOrderId($shopOrderId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
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

  function selectByShopOrderIdAndShopItemId($shopOrderId, $shopItemId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByShopOrderIdAndShopItemId($shopOrderId, $shopItemId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
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

  function selectLikePattern($searchPattern) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern)) {
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

    return($this->dao->insert($object->getName(), $object->getShortDescription(), $object->getReference(), $object->getPrice(), $object->getVatRate(), $object->getShippingFee(), $object->getQuantity(), $object->getIsGift(), $object->getOptions(), $object->getShopOrderId(), $object->getShopItemId(), $object->getImageUrl()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getShortDescription(), $object->getReference(), $object->getPrice(), $object->getVatRate(), $object->getShippingFee(), $object->getQuantity(), $object->getIsGift(), $object->getOptions(), $object->getShopOrderId(), $object->getShopItemId(), $object->getImageUrl()));
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
