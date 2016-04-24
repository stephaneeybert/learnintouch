<?

class ElearningCourseInfoDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function ElearningCourseInfoDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_ELEARNING_COURSE_INFO;

    $this->dao = new ElearningCourseInfoDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new ElearningCourseInfo();
      $object->setId($row['id']);
      $object->setHeadline($row['headline']);
      $object->setInformation($row['information']);
      $object->setListOrder($row['list_order']);
      $object->setElearningCourseId($row['elearning_course_id']);

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

  function selectByNextListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($elearningCourseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($elearningCourseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($elearningCourseId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByListOrder($elearningCourseId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  // Reset the list order of all the objects if some mistakenly share the same list order
  function resetListOrder($elearningCourseId) {
    if ($elearningCourseId && $this->countDuplicateListOrderRows($elearningCourseId) > 0) {
      if ($elearningCourseInfos = $this->selectByCourseIdOrderById($elearningCourseId)) {
        if (count($elearningCourseInfos) > 0) {
          $listOrder = 0;
          foreach ($elearningCourseInfos as $elearningCourseInfo) {
            $listOrder = $listOrder + 1;
            $elearningCourseInfo->setListOrder($listOrder);
            $this->update($elearningCourseInfo);
          }
        }
      }
    }
  }

  // Count the number of objects that have the same list order
  // There should be zero if the ordering of the objects is correct
  function countDuplicateListOrderRows($elearningCourseId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningCourseId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByCourseIdOrderById($elearningCourseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseIdOrderById($elearningCourseId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByCourseId($elearningCourseId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByCourseId($elearningCourseId)) {
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

    $information = $object->getInformation();
    $information = LibString::databaseEscapeQuotes($information);

    return($this->dao->insert($object->getHeadline(), $information, $object->getListOrder(), $object->getElearningCourseId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $information = $object->getInformation();
    $information = LibString::databaseEscapeQuotes($information);

    return($this->dao->update($object->getId(), $object->getHeadline(), $information, $object->getListOrder(), $object->getElearningCourseId()));
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
