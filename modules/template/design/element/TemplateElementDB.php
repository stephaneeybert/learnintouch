<?

class TemplateElementDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplateElementDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_TEMPLATE_ELEMENT;

    $this->dao = new TemplateElementDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplateElement();
      $object->setId($row['id']);
      $object->setElementType($row['element_type']);
      $object->setObjectId($row['object_id']);
      $object->setTemplateContainerId($row['template_container_id']);
      $object->setListOrder($row['list_order']);
      $object->setHide($row['hide']);

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

  function selectByTemplateContainerId($templateContainerId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplateContainerId($templateContainerId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByTemplateContainerIdOrderById($templateContainerId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplateContainerIdOrderById($templateContainerId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNextListOrder($templateContainerId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($templateContainerId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($templateContainerId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($templateContainerId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($templateContainerId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($templateContainerId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($templateContainerId) {
    if ($this->countDuplicateListOrderRows($templateContainerId) > 0) {
      if ($templateElements = $this->selectByTemplateContainerIdOrderById($templateContainerId)) {
        if (count($templateElements) > 0) {
          $listOrder = 0;
          foreach ($templateElements as $templateElement) {
            $listOrder = $listOrder + 1;
            $templateElement->setListOrder($listOrder);
            $this->update($templateElement);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($templateContainerId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($templateContainerId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getElementType(), $object->getObjectId(), $object->getTemplateContainerId(), $object->getListOrder(), $object->getHide()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getElementType(), $object->getObjectId(), $object->getTemplateContainerId(), $object->getListOrder(), $object->getHide()));
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
