<?

class ContactDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_CONTACT;

    $this->dao = new ContactDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Contact();
      $object->setId($row['id']);
      $object->setFirstname($row['firstname']);
      $object->setLastname($row['lastname']);
      $object->setEmail($row['email']);
      $object->setOrganisation($row['organisation']);
      $object->setTelephone($row['telephone']);
      $object->setSubject($row['subject']);
      $object->setMessage($row['message']);
      $object->setContactDate($row['contact_datetime']);
      $object->setStatus($row['contact_status_id']);
      $object->setContactRefererId($row['contact_referer_id']);
      $object->setGarbage($row['garbage']);

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

  function countFoundRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countFoundRows();
    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectAll($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAll($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectNonGarbage($start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNonGarbage($start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByStatus($status, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByStatus($status, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAllByStatusId($status) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllByStatusId($status)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAllByRefererId($status) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllByRefererId($status)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectGarbage() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectGarbage()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function deleteByDate($sinceDate) {
    $this->dataSource->selectDatabase();

    return($this->dao->deleteByDate($sinceDate));
  }

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $subject = $object->getSubject();
    $subject = LibString::databaseEscapeQuotes($subject);

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);

    return($this->dao->insert($object->getFirstname(), $object->getLastname(), $object->getEmail(), $object->getOrganisation(), $object->getTelephone(), $subject, $message, $object->getContactDate(), $object->getStatus(), $object->getContactRefererId(), $object->getGarbage()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $subject = $object->getSubject();
    $subject = LibString::databaseEscapeQuotes($subject);

    $message = $object->getMessage();
    $message = LibString::databaseEscapeQuotes($message);

    return($this->dao->update($object->getId(), $object->getFirstname(), $object->getLastname(), $object->getEmail(), $object->getOrganisation(), $object->getTelephone(), $subject, $message, $object->getContactDate(), $object->getStatus(), $object->getContactRefererId(), $object->getGarbage()));
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
