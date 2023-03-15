<?

class NavlinkItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_NAVLINK_ITEM;

    $this->dao = new NavlinkItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NavlinkItem();
      $object->setId($row['id']);
      $object->setText($row['name']);
      $object->setDescription($row['description']);
      $object->setImage($row['image']);
      $object->setImageOver($row['image_over']);
      $object->setUrl($row['url']);
      $object->setBlankTarget($row['blank_target']);
      $object->setLanguage($row['language_code']);
      $object->setTemplateModelId($row['template_model_id']);
      $object->setNavlinkId($row['navlink_id']);

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

  function selectByNoLanguageAndNavlinkId($navlinkId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNoLanguageAndNavlinkId($navlinkId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByLanguageAndNavlinkId($language, $navlinkId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLanguageAndNavlinkId($language, $navlinkId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByNavlinkId($navlinkId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNavlinkId($navlinkId)) {
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

    return($this->dao->insert($object->getText(), $object->getDescription(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getLanguage(), $object->getTemplateModelId(), $object->getNavlinkId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getText(), $object->getDescription(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getLanguage(), $object->getTemplateModelId(), $object->getNavlinkId()));
  }

  function resetNavigationModelReferences($templateModelId) {
    $this->dataSource->selectDatabase();
    return($this->dao->resetNavigationModelReferences($templateModelId));
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
