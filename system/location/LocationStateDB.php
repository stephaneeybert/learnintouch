<?

class LocationStateDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function LocationStateDB() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->tableName = DB_TABLE_LOCATION_STATE;

    $this->dao = new LocationStateDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new LocationState();
      $object->setId($row['id']);
      $object->setCode($row['code']);
      $object->setName($row['name']);
      $object->setUpperName($row['upper_name']);
      $object->setRegion($row['region']);
      $object->setCountry($row['country']);

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

  function selectByCode($code) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByCode($code)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByName($name)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
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

  function selectByRegion($region) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByRegion($region)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCountryAndRegion($country, $region) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCountryAndRegion($country, $region)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCountry($country) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCountry($country)) {
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

    return($this->dao->insert($object->getCode(), $object->getName(), $object->getUpperName(), $object->getRegion(), $object->getCountry()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getCode(), $object->getName(), $object->getUpperName(), $object->getRegion(), $object->getCountry()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

}

?>
