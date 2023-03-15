<?

class AddressDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ADDRESS;

    $this->dao = new AddressDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Address();
      $object->setId($row['id']);
      $object->setAddress1($row['address1']);
      $object->setAddress2($row['address2']);
      $object->setZipCode($row['zip_code']);
      $object->setCity($row['city']);
      $object->setState($row['state']);
      $object->setCountry($row['country']);
      $object->setPostalBox($row['postal_box']);

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

  function insert($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getAddress1(), $object->getAddress2(), $object->getZipCode(), $object->getCity(), $object->getState(), $object->getCountry(), $object->getPostalBox()));
  }

  function update($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getAddress1(), $object->getAddress2(), $object->getZipCode(), $object->getCity(), $object->getState(), $object->getCountry(), $object->getPostalBox()));
  }

  function delete($id) {
    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
