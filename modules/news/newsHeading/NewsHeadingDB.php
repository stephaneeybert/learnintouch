<?

class NewsHeadingDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsHeadingDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_NEWS_HEADING;

    $this->dao = new NewsHeadingDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsHeading();
      $object->setId($row['id']);
      $object->setListOrder($row['list_order']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setImage($row['image']);
      $object->setNewsPublicationId($row['news_publication_id']);

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

  function selectByNextListOrder($id, $newsPublicationId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($id, $newsPublicationId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPreviousListOrder($id, $newsPublicationId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($id, $newsPublicationId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByListOrder($id, $newsPublicationId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($id, $newsPublicationId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($newsPublicationId) {
    if ($this->countDuplicateListOrderRows($newsPublicationId) > 0) {
      if ($newsHeadings = $this->selectByNewsPublicationIdOrderById($newsPublicationId)) {
        if (count($newsHeadings) > 0) {
          $listOrder = 0;
          foreach ($newsHeadings as $newsHeading) {
            $listOrder = $listOrder + 1;
            $newsHeading->setListOrder($listOrder);
            $this->update($newsHeading);
            }
          }
        }
      }
    }

  function countDuplicateListOrderRows($newsPublicationId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($newsPublicationId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
    }

  function selectByNewsPublicationId($newsPublicationId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationId($newsPublicationId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsPublicationIdOrderById($newsPublicationId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationIdOrderById($newsPublicationId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->insert($object->getListOrder(), $object->getName(), $object->getDescription(), $object->getImage(), $object->getNewsPublicationId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getListOrder(), $object->getName(), $object->getDescription(), $object->getImage(), $object->getNewsPublicationId()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
