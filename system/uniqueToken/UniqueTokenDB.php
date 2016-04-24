<?

class UniqueTokenDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function UniqueTokenDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_UNIQUE_TOKEN;

    $this->dao = new UniqueTokenDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new UniqueToken();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setValue($row['value']);
      $object->setCreationDateTime($row['creation_datetime']);
      $object->setExpirationDateTime($row['expiration_datetime']);

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

  function selectByNameAndValue($name, $value) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNameAndValue($name, $value)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->insert($object->getName(), $object->getValue(), $object->getCreationDateTime(), $object->getExpirationDateTime()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getName(), $object->getValue(), $object->getCreationDateTime(), $object->getExpirationDateTime()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
