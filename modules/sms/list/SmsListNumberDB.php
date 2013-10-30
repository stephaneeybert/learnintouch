<?

class SmsListNumberDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function SmsListNumberDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_SMS_LIST_NUMBER;

    $this->dao = new SmsListNumberDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new SmsListNumber();
      $object->setId($row['id']);
      $object->setSmsListId($row['sms_list_id']);
      $object->setSmsNumberId($row['sms_number_id']);

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

  function selectBySmsListId($smsListId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySmsListId($smsListId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectBySmsNumberId($smsNumberId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectBySmsNumberId($smsNumberId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectBySmsListIdAndSmsNumberId($smsListId, $smsNumberId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectBySmsListIdAndSmsNumberId($smsListId, $smsNumberId)) {
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

    return($this->dao->insert($object->getSmsListId(), $object->getSmsNumberId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getSmsListId(), $object->getSmsNumberId()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  function deleteBySmsListId($smsListId) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteBySmsListId($smsListId));
    }

  }

?>
