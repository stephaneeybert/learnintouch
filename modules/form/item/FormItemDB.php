<?

class FormItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_FORM_ITEM;

    $this->dao = new FormItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new FormItem();
      $object->setId($row['id']);
      $object->setType($row['type']);
      $object->setName($row['name']);
      $object->setText($row['text']);
      $object->setHelp($row['help']);
      $object->setDefaultValue($row['default_value']);
      $object->setSize($row['item_size']);
      $object->setMaxlength($row['maxlength']);
      $object->setListOrder($row['list_order']);
      $object->setInMailAddress($row['in_mail_address']);
      $object->setMailListId($row['mail_list_id']);
      $object->setFormId($row['form_id']);

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

  function selectByFormId($formId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByFormId($formId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }

      return($objects);
    }
  }

  function selectByFormIdOrderById($formId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByFormIdOrderById($formId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }

      return($objects);
    }
  }

  function selectByNextListOrder($formId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($formId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($formId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($formId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($formId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($formId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($formId) {
    if ($this->countDuplicateListOrderRows($formId) > 0) {
      if ($formItems = $this->selectByFormIdOrderById($formId)) {
        if (count($formItems) > 0) {
          $listOrder = 0;
          foreach ($formItems as $formItem) {
            $listOrder = $listOrder + 1;
            $formItem->setListOrder($listOrder);
            $this->update($formItem);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($formId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($formId);

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

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    return($this->dao->insert($object->getType(), $object->getName(), $text, $object->getHelp(), $object->getDefaultValue(), $object->getSize(), $object->getMaxlength(), $object->getListOrder(), $object->getInMailAddress(), $object->getMailListId(), $object->getFormId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    return($this->dao->update($object->getId(), $object->getType(), $object->getName(), $text, $object->getHelp(), $object->getDefaultValue(), $object->getSize(), $object->getMaxlength(), $object->getListOrder(), $object->getInMailAddress(), $object->getMailListId(), $object->getFormId()));
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
