<?

class NavmenuItemDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_NAVMENU_ITEM;

    $this->dao = new NavmenuItemDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NavmenuItem();
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
      $object->setParentNavmenuItemId($row['parent_id']);

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

  function selectByNextListOrder($navmenuId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($navmenuId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPreviousListOrder($navmenuId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($navmenuId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByListOrder($navmenuId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($navmenuId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($parentNavmenuItemId) {
    if ($this->countDuplicateListOrderRows($parentNavmenuItemId) > 0) {
      if ($navmenuItems = $this->selectByParentNavmenuItemIdOrderById($parentNavmenuItemId)) {
        if (count($navmenuItems) > 0) {
          $listOrder = 0;
          foreach ($navmenuItems as $navmenuItem) {
            $listOrder = $listOrder + 1;
            $navmenuItem->setListOrder($listOrder);
            $this->update($navmenuItem);
            }
          }
        }
      }
    }

  function countDuplicateListOrderRows($parentNavmenuItemId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($parentNavmenuItemId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function selectByParentNavmenuItemId($parentNavmenuItemId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByParentNavmenuItemIdOrderById($parentNavmenuItemId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByParentNavmenuItemIdOrderById($parentNavmenuItemId)) {
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

    return($this->dao->insert($object->getName(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getDescription(), $object->getHide(), $object->getTemplateModelId(), $object->getListOrder(), $object->getParentNavmenuItemId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getName(), $object->getImage(), $object->getImageOver(), $object->getUrl(), $object->getBlankTarget(), $object->getDescription(), $object->getHide(), $object->getTemplateModelId(), $object->getListOrder(), $object->getParentNavmenuItemId()));
    }

  function resetNavigationModelReferences($templateModelId) {
    $this->dataSource->selectDatabase();
    return($this->dao->resetNavigationModelReferences($templateModelId));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    // Delete all the sub items of the item
    if ($navmenuItems = $this->selectByParentNavmenuItemId($id)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $this->delete($navmenuItemId);
	      }
      }

    $this->dao->delete($id);
    }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
    }

  }

?>
