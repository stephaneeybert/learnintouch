<?

class NavbarItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NavbarItemDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NAVBAR_ITEM;

    $this->dao = new NavbarItemDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NavbarItem();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setImage($row['image']);
      $object->setImageOver($row['image_over']);
      $object->setUrl($row['url']);
      $object->setBlankTarget($row['blank_target']);
      $object->setDescription($row['description']);
      $object->setHide($row['hide']);
      $object->setTemplateModelId($row['template_model_id']);
      $object->setListOrder($row['list_order']);
      $object->setNavbarLanguageId($row['navbar_language_id']);

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

  function selectByNextListOrder($navbarLanguageId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($navbarLanguageId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($navbarLanguageId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($navbarLanguageId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($navbarLanguageId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($navbarLanguageId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($navbarLanguageId) {
    if ($this->countDuplicateListOrderRows($navbarLanguageId) > 0) {
      if ($navbarItems = $this->selectByNavbarLanguageIdOrderById($navbarLanguageId)) {
        if (count($navbarItems) > 0) {
          $listOrder = 0;
          foreach ($navbarItems as $navbarItem) {
            $listOrder = $listOrder + 1;
            $navbarItem->setListOrder($listOrder);
            $this->update($navbarItem);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($navbarLanguageId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($navbarLanguageId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByNavbarLanguageId($navbarLanguageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNavbarLanguageId($navbarLanguageId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNavbarLanguageIdOrderById($navbarLanguageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNavbarLanguageIdOrderById($navbarLanguageId)) {
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

    return($this->dao->insert($object->getName(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getDescription(), $object->getHide(), $object->getTemplateModelId(), $object->getListOrder(), $object->getNavbarLanguageId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getDescription(), $object->getHide(), $object->getTemplateModelId(), $object->getListOrder(), $object->getNavbarLanguageId()));
  }

  function resetNavigationModelReferences($templateModelId) {
    $this->dataSource->selectDatabase();

    return($this->dao->resetNavigationModelReferences($templateModelId));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

}

?>
