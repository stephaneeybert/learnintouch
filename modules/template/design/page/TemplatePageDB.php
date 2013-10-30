<?

class TemplatePageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplatePageDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_TEMPLATE_PAGE;

    $this->dao = new TemplatePageDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplatePage();
      $object->setId($row['id']);
      $object->setSystemPage($row['system_page']);
      $object->setTemplateModelId($row['template_model_id']);

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

  function selectByTemplateModelId($templateModelId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplateModelId($templateModelId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
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

    return($this->dao->insert($object->getSystemPage(), $object->getTemplateModelId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getSystemPage(), $object->getTemplateModelId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

  function getTemplateModelId($templatePageId) {
    $templateModelId = '';

    if ($templatePage = $this->selectById($templatePageId)) {
      $templateModelId = $templatePage->getTemplateModelId();
    }

    return($templateModelId);
  }

}

?>
