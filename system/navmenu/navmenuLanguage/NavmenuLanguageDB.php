<?

class NavmenuLanguageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NavmenuLanguageDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NAVMENU_LANGUAGE;

    $this->dao = new NavmenuLanguageDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NavmenuLanguage();
      $object->setId($row['id']);
      $object->setLanguage($row['language_code']);
      $object->setNavmenuId($row['navmenu_id']);
      $object->setNavmenuItemId($row['navmenu_item_id']);

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

  function selectByNavmenuId($navmenuId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNavmenuId($navmenuId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNavmenuIdAndNoLanguage($navmenuId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNavmenuIdAndNoLanguage($navmenuId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByNavmenuIdAndLanguage($navmenuId, $language) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNavmenuIdAndLanguage($navmenuId, $language)) {
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

    return($this->dao->insert($object->getLanguage(), $object->getNavmenuId(), $object->getNavmenuItemId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getLanguage(), $object->getNavmenuId(), $object->getNavmenuItemId()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    $this->dao->delete($id);
  }

}

?>
