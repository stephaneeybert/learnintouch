<?

class NewsStoryImageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsStoryImageDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NEWS_STORY_IMAGE;

    $this->dao = new NewsStoryImageDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsStoryImage();
      $object->setId($row['id']);
      $object->setImage($row['image']);
      $object->setDescription($row['description']);
      $object->setListOrder($row['list_order']);
      $object->setNewsStoryId($row['news_story_id']);

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

  function selectByNextListOrder($newsStoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($newsStoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPreviousListOrder($newsStoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($newsStoryId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByListOrder($newsStoryId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($newsStoryId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($newsStoryId) {
    if ($this->countDuplicateListOrderRows($newsStoryId) > 0) {
      if ($newsStoryImages = $this->selectByNewsStoryIdOrderById($newsStoryId)) {
        if (count($newsStoryImages) > 0) {
          $listOrder = 0;
          foreach ($newsStoryImages as $newsStoryImage) {
            $listOrder = $listOrder + 1;
            $newsStoryImage->setListOrder($listOrder);
            $this->update($newsStoryImage);
            }
          }
        }
      }
    }

  function countDuplicateListOrderRows($newsStoryId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($newsStoryId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function selectByNewsStoryId($newsStoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsStoryId($newsStoryId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsStoryIdOrderById($newsStoryId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsStoryIdOrderById($newsStoryId)) {
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

    return($this->dao->insert($object->getImage(), $object->getDescription(), $object->getListOrder(), $object->getNewsStoryId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getImage(), $object->getDescription(), $object->getListOrder(), $object->getNewsStoryId()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
