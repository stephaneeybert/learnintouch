<?

class TemplateContainerDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplateContainerDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_TEMPLATE_CONTAINER;

    $this->dao = new TemplateContainerDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplateContainer();
      $object->setId($row['id']);
      $object->setTemplateModelId($row['template_model_id']);
      $object->setRow($row['row_nb']);
      $object->setCell($row['cell_nb']);
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

  function selectByNextCell($templateModelId, $row, $cell) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextCell($templateModelId, $row, $cell)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousCell($templateModelId, $row, $cell) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousCell($templateModelId, $row, $cell)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByModelIdAndRow($templateModelId, $row) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByModelIdAndRow($templateModelId, $row)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByModelIdAndRowAndCell($templateModelId, $row, $cell) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectNbCellsByRow($templateModelId) {
    $this->dataSource->selectDatabase();

    $rowNbCells = Array();
    if ($result = $this->dao->selectNbCellsByRow($templateModelId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        if ($row && is_array($row)) {
          $rowNb = $row['rowNb'];
          $nbCells = $row['nbCells'];
          $rowNbCells[$rowNb] = $nbCells;
        }
      }
    }

    return($rowNbCells);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getTemplateModelId(), $object->getRow(), $object->getCell(), $object->getTemplatePropertySetId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getTemplateModelId(), $object->getRow(), $object->getCell(), $object->getTemplatePropertySetId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

  function getTemplateModelId($templateContainerId) {
    $templateModelId = '';

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templateModelId = $templateContainer->getTemplateModelId();
    }

    return($templateModelId);
  }

}

?>
