<?

class WebsiteAddressDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function WebsiteAddressDB() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->tableName = DB_TABLE_WEBSITE_ADDRESS;

    $this->dao = new WebsiteAddressDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new WebsiteAddress();
      $object->setId($row['id']);
      $object->setAddress1($row['address1']);
      $object->setAddress2($row['address2']);
      $object->setZipCode($row['zip_code']);
      $object->setCity($row['city']);
      $object->setState($row['state']);
      $object->setCountry($row['country']);
      $object->setPostalBox($row['postal_box']);
      $object->setTelephone($row['telephone']);
      $object->setMobile($row['mobile']);
      $object->setFax($row['fax']);
      $object->setVatNumber($row['vat_number']);
      $object->setWebsite($row['website_id']);

      return($object);
    }
  }

  function selectById($id) {
    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByWebsite($websiteId) {
    if ($result = $this->dao->selectByWebsite($websiteId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectAll() {
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

  function insert($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getAddress1(), $object->getAddress2(), $object->getZipCode(), $object->getCity(), $object->getState(), $object->getCountry(), $object->getPostalBox(), $object->getTelephone(), $object->getMobile(), $object->getFax(), $object->getVatNumber(), $object->getWebsite()));
  }

  function update($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getAddress1(), $object->getAddress2(), $object->getZipCode(), $object->getCity(), $object->getState(), $object->getCountry(), $object->getPostalBox(), $object->getTelephone(), $object->getMobile(), $object->getFax(), $object->getVatNumber(), $object->getWebsite()));
  }

  function delete($id) {
    return($this->dao->delete($id));
  }

}

?>
