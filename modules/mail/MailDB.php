<?

class MailDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function MailDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_MAIL;

    $this->dao = new MailDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Mail();
      $object->setId($row['id']);
      $object->setSubject($row['subject']);
      $object->setBody($row['body']);
      $object->setDescription($row['description']);
      $object->setTextFormat($row['text_format']);
      $object->setAttachments($row['attachments']);
      $object->setCreationDate($row['creation_datetime']);
      $object->setSendDate($row['send_datetime']);
      $object->setLocked($row['locked']);
      $object->setAdminId($row['admin_id']);
      $object->setCategoryId($row['category_id']);

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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByAdminId($adminId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAdminId($adminId, $start, $rows)) {
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

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryId($categoryId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByAdminIdAndCategoryId($adminId, $categoryId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByAdminIdAndCategoryId($adminId, $categoryId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $subject = $object->getSubject();
    $subject = LibString::databaseEscapeQuotes($subject);

    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);

    return($this->dao->insert($subject, $body, $object->getDescription(), $object->getTextFormat(), $object->getAttachments(), $object->getCreationDate(), $object->getSendDate(), $object->getLocked(), $object->getAdminId(), $object->getCategoryId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $subject = $object->getSubject();
    $subject = LibString::databaseEscapeQuotes($subject);

    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);

    return($this->dao->update($object->getId(), $subject, $body, $object->getDescription(), $object->getTextFormat(), $object->getAttachments(), $object->getCreationDate(), $object->getSendDate(), $object->getLocked(), $object->getAdminId(), $object->getCategoryId()));
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
