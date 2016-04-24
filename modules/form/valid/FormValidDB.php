<?

class FormValidDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function FormValidDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_FORM_VALID;

    $this->dao = new FormValidDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new FormValid();
      $object->setId($row['id']);
      $object->setType($row['type']);
      $object->setMessage($row['message']);
      $object->setBoundary($row['boundary']);
      $object->setFormItemId($row['form_item_id']);

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

  function selectByFormItemId($formItemId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByFormItemId($formItemId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }

      return($objects);
    }
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);

    return($this->dao->insert($object->getType(), $message, $object->getBoundary(), $object->getFormItemId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);

    return($this->dao->update($object->getId(), $object->getType(), $message, $object->getBoundary(), $object->getFormItemId()));
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
