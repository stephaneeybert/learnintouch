<?

class TemplatePropertyDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplatePropertyDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_TEMPLATE_PROPERTY;

    $this->dao = new TemplatePropertyDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplateProperty();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setValue($row['value']);
      $object->setTemplatePropertySetId($row['template_property_set_id']);

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

  function selectByValue($value) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByValue($value)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByTemplatePropertySetIdAndName($templatePropertySetId, $name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByTemplatePropertySetIdAndName($templatePropertySetId, $name)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByTemplatePropertySetId($templatePropertySetId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplatePropertySetId($templatePropertySetId)) {
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

    return($this->dao->insert($object->getName(), $object->getValue(), $object->getTemplatePropertySetId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getValue(), $object->getTemplatePropertySetId()));
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
