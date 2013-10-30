<?

class LinkDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function LinkDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_LINK;

    $this->dao = new LinkDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new Link();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setImage($row['image']);
      $object->setUrl($row['url']);
      $object->setCategoryId($row['category_id']);
      $object->setListOrder($row['list_order']);

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

  function selectByNextListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($categoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPreviousListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($categoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByListOrder($categoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($categoryId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($categoryId) {
    if ($this->countDuplicateListOrderRows($categoryId) > 0) {
      if ($links = $this->selectByCategoryIdOrderById($categoryId)) {
        if (count($links) > 0) {
          $listOrder = 0;
          foreach ($links as $link) {
            $listOrder = $listOrder + 1;
            $link->setListOrder($listOrder);
            $this->update($link);
            }
          }
        }
      }
    }

  function countDuplicateListOrderRows($categoryId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($categoryId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
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

  function selectByCategoryId($categoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryId($categoryId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByCategoryIdOrderById($categoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCategoryIdOrderById($categoryId)) {
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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getImage(), $object->getUrl(), $object->getCategoryId(), $object->getListOrder()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getImage(), $object->getUrl(), $object->getCategoryId(), $object->getListOrder()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
