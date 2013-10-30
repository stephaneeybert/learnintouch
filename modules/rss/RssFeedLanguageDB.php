<?

class RssFeedLanguageDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function RssFeedLanguageDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_RSS_FEED_LANGUAGE;

    $this->dao = new RssFeedLanguageDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new RssFeedLanguage();
      $object->setId($row['id']);
      $object->setLanguage($row['language_code']);
      $object->setTitle($row['title']);
      $object->setUrl($row['url']);
      $object->setRssFeedId($row['rss_feed_id']);

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

  function selectByRssFeedId($rssFeedId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByRssFeedId($rssFeedId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByLanguageAndRssFeedId($language, $rssFeedId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByLanguageAndRssFeedId($language, $rssFeedId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByNoLanguageAndRssFeedId($rssFeedId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNoLanguageAndRssFeedId($rssFeedId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getLanguage(), $object->getTitle(), $object->getUrl(), $object->getRssFeedId()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getLanguage(), $object->getTitle(), $object->getUrl(), $object->getRssFeedId()));
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
