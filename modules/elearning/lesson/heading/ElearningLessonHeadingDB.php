<?

class ElearningLessonHeadingDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningLessonHeadingDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_ELEARNING_LESSON_HEADING;

    $this->dao = new ElearningLessonHeadingDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningLessonHeading();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setContent($row['content']);
      $object->setListOrder($row['list_order']);
      $object->setImage($row['image']);
      $object->setElearningLessonModelId($row['elearning_lesson_model_id']);

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

  function selectByNextListOrder($id, $elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($id, $elearningLessonModelId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($id, $elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($id, $elearningLessonModelId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($id, $elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($id, $elearningLessonModelId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($elearningLessonModelId) {
    if ($this->countDuplicateListOrderRows($elearningLessonModelId) > 0) {
      if ($elearningLessonHeadings = $this->selectByElearningLessonModelIdOrderById($elearningLessonModelId)) {
        if (count($elearningLessonHeadings) > 0) {
          $listOrder = 0;
          foreach ($elearningLessonHeadings as $elearningLessonHeading) {
            $listOrder = $listOrder + 1;
            $elearningLessonHeading->setListOrder($listOrder);
            $this->update($elearningLessonHeading);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($elearningLessonModelId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningLessonModelId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByElearningLessonModelId($elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByElearningLessonModelId($elearningLessonModelId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByElearningLessonModelIdOrderById($elearningLessonModelId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByElearningLessonModelIdOrderById($elearningLessonModelId)) {
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

    $content = $object->getContent();
    $content = LibString::databaseEscapeQuotes($content);

    return($this->dao->insert($object->getName(), $content, $object->getListOrder(), $object->getImage(), $object->getElearningLessonModelId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $content = $object->getContent();
    $content = LibString::databaseEscapeQuotes($content);

    return($this->dao->update($object->getId(), $object->getName(), $content, $object->getListOrder(), $object->getImage(), $object->getElearningLessonModelId()));
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
