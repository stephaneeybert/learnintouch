<?

class TemplatePropertySetDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplatePropertySetDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_TEMPLATE_PROPERTY_SET;

    $this->dao = new TemplatePropertySetDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplatePropertySet();
      $object->setId($row['id']);

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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert());
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function cleanup() {
    $this->dataSource->selectDatabase();

    return($this->dao->cleanup());
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
