<?

class ShopDiscountDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ShopDiscountDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_SHOP_DISCOUNT;

    $this->dao = new ShopDiscountDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopDiscount();
      $object->setId($row['id']);
      $object->setDiscountCode($row['discount_code']);
      $object->setDiscountRate($row['discount_rate']);
      $object->setShopAffiliateId($row['shop_affiliate_id']);

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

  function selectByDiscountCode($discountCode) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByDiscountCode($discountCode)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByAffiliateId($shopAffiliateId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAffiliateId($shopAffiliateId)) {
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

    return($this->dao->insert($object->getDiscountCode(), $object->getDiscountRate(), $object->getShopAffiliateId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getDiscountCode(), $object->getDiscountRate(), $object->getShopAffiliateId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

}

?>
