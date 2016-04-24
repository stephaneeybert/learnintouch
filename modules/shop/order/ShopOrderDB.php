<?

class ShopOrderDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ShopOrderDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_SHOP_ORDER;

    $this->dao = new ShopOrderDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ShopOrder();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setOrganisation($row['organisation']);
      $object->setVatNumber($row['vat_number']);
      $object->setEmail($row['email']);
      $object->setTelephone($row['telephone']);
      $object->setMobilePhone($row['mobile_phone']);
      $object->setFax($row['fax']);
      $object->setMessage($row['message']);
      $object->setHandlingFee($row['handling_fee']);
      $object->setDiscountCode($row['discount_code']);
      $object->setDiscountAmount($row['discount_amount']);
      $object->setCurrency($row['currency']);
      $object->setInvoiceNumber($row['invoice_number']);
      $object->setInvoiceNote($row['invoice_note']);
      $object->setInvoiceLanguage($row['invoice_language_code']);
      $object->setInvoiceAddressId($row['invoice_address_id']);
      $object->setShippingAddressId($row['shipping_address_id']);
      $object->setOrderDate($row['order_date']);
      $object->setDueDate($row['due_date']);
      $object->setClientIP($row['client_ip']);
      $object->setStatus($row['status']);
      $object->setPaymentType($row['payment_type']);
      $object->setPaymentTransactionID($row['payment_transaction_id']);
      $object->setUserId($row['user_account_id']);

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

  function selectByEmail($email, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByEmail($email, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByStatus($status, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByStatus($status, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByStatusAndYearAndMonth($status, $year, $month) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByStatusAndYearAndMonth($status, $year, $month)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByYearAndMonth($year, $month) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByYearAndMonth($year, $month)) {
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

  function selectByUserId($userId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByUserId($userId)) {
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

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getOrganisation(), $object->getVatNumber(), $object->getEmail(), $object->getTelephone(), $object->getMobilePhone(), $object->getFax(), $object->getMessage(), $object->getHandlingFee(), $object->getDiscountCode(), $object->getDiscountAmount(), $object->getCurrency(), $object->getInvoiceNumber(), $object->getInvoiceNote(), $object->getInvoiceLanguage(), $object->getInvoiceAddressId(), $object->getShippingAddressId(), $object->getOrderDate(), $object->getDueDate(), $object->getClientIP(), $object->getStatus(), $object->getPaymentType(), $object->getPaymentTransactionID(), $object->getUserId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(), $object->getOrganisation(), $object->getVatNumber(), $object->getEmail(), $object->getTelephone(), $object->getMobilePhone(), $object->getFax(), $object->getMessage(), $object->getHandlingFee(), $object->getDiscountCode(), $object->getDiscountAmount(), $object->getCurrency(), $object->getInvoiceNumber(), $object->getInvoiceNote(), $object->getInvoiceLanguage(), $object->getInvoiceAddressId(), $object->getShippingAddressId(), $object->getOrderDate(), $object->getDueDate(), $object->getClientIP(), $object->getStatus(), $object->getPaymentType(), $object->getPaymentTransactionID(), $object->getUserId()));
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
