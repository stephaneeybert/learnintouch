<?

class FormDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function FormDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_FORM;

    $this->dao = new FormDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Form();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setTitle($row['title']);
      $object->setImage($row['image']);
      $object->setDescription($row['description']);
      $object->setEmail($row['email']);
      $object->setInstructions($row['instructions']);
      $object->setAcknowledge($row['acknowledge']);
      $object->setWebpageId($row['webpage_id']);
      $object->setMailSubject($row['mail_subject']);
      $object->setMailMessage($row['mail_message']);

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

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByName($name)) {
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

  function countAll() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countAll();

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

    $title = $object->getTitle();
    $title = LibString::databaseEscapeQuotes($title);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    $acknowledge = $object->getAcknowledge();
    $acknowledge = LibString::databaseEscapeQuotes($acknowledge);

    return($this->dao->insert($object->getName(), $object->getDescription(), $title, $object->getImage(), $object->getEmail(), $instructions, $acknowledge, $object->getWebpageId(), $object->getMailSubject(), $object->getMailMessage()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $title = $object->getTitle();
    $title = LibString::databaseEscapeQuotes($title);

    $instructions = $object->getInstructions();
    $instructions = LibString::databaseEscapeQuotes($instructions);

    $acknowledge = $object->getAcknowledge();
    $acknowledge = LibString::databaseEscapeQuotes($acknowledge);

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $title, $object->getImage(), $object->getEmail(), $instructions, $acknowledge, $object->getWebpageId(), $object->getMailSubject(), $object->getMailMessage()));
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
