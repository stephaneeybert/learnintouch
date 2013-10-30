<?

class FormItemValueDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function FormItemValueDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_FORM_ITEM_VALUE;

    $this->dao = new FormItemValueDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new FormItemValue();
      $object->setId($row['id']);
      $object->setValue($row['value']);
      $object->setText($row['text']);
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

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    return($this->dao->insert($object->getValue(), $text, $object->getFormItemId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $text = $object->getText();
    $text = LibString::databaseEscapeQuotes($text);

    return($this->dao->update($object->getId(), $object->getValue(), $text, $object->getFormItemId()));
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
