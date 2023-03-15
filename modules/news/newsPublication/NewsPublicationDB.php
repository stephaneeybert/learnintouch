<?

class NewsPublicationDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_NEWS_PUBLICATION;

    $this->dao = new NewsPublicationDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsPublication();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setDescription($row['description']);
      $object->setNbColumns($row['nb_columns']);
      $object->setSlideDown($row['slide_down']);
      $object->setAlign($row['align']);
      $object->setWithArchive($row['with_archive']);
      $object->setWithOthers($row['with_others']);
      $object->setWithByHeading($row['with_by_heading']);
      $object->setHideHeading($row['hide_heading']);
      $object->setAutoArchive($row['auto_archive']);
      $object->setAutoDelete($row['auto_delete']);
      $object->setSecured($row['secured']);

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

  function selectLikePattern($searchPattern) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern)) {
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

  function selectByNotPublished() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNotPublished()) {
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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getNbColumns(), $object->getSlideDown(), $object->getAlign(), $object->getWithArchive(), $object->getWithOthers(), $object->getWithByHeading(), $object->getHideHeading(), $object->getAutoArchive(), $object->getAutoDelete(), $object->getSecured()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getNbColumns(), $object->getSlideDown(), $object->getAlign(), $object->getWithArchive(), $object->getWithOthers(), $object->getWithByHeading(), $object->getHideHeading(), $object->getAutoArchive(), $object->getAutoDelete(), $object->getSecured()));
  }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
  }

}

?>
