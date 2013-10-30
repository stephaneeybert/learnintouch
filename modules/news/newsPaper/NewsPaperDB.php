<?

class NewsPaperDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsPaperDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NEWS_PAPER;

    $this->dao = new NewsPaperDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsPaper();
      $object->setId($row['id']);
      $object->setTitle($row['title']);
      $object->setImage($row['image']);
      $object->setHeader($row['header']);
      $object->setFooter($row['footer']);
      $object->setReleaseDate($row['release_date']);
      $object->setArchive($row['archive_date']);
      $object->setNotPublished($row['not_published']);
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

  function selectByTitle($title) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByTitle($title)) {
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

  function countFoundRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countFoundRows();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
      }

    return($count);
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

  function selectLikePatternInNewsPaperAndNewsPublication($searchPattern, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternInNewsPaperAndNewsPublication($searchPattern, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectRecentReleases($newsPublicationId, $systemDate) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectRecentReleases($newsPublicationId, $systemDate)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePattern($searchPattern, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByPatternAndNewsPublicationId($searchPattern, $newsPublicationId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByPatternAndNewsPublicationId($searchPattern, $newsPublicationId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsPublicationId($newsPublicationId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationId($newsPublicationId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByPublished($systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByPublished($systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsPublicationAndPublished($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationAndPublished($newsPublicationId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByArchived($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByArchived($newsPublicationId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByDeferred($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByDeferred($newsPublicationId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsPublicationIdAndPublish($newsPublicationId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationIdAndPublish($newsPublicationId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectByNewsPublicationIdAndNotPublish($newsPublicationId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPublicationIdAndNotPublish($newsPublicationId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function selectLikePatternAndPublished($searchPattern, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectLikePatternAndPublished($searchPattern, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }

    return($objects);
    }

  function archiveByReleaseDate($newsPublicationId, $sinceDate, $systemDate) {
    $this->dataSource->selectDatabase();

    return($this->dao->archiveByReleaseDate($newsPublicationId, $sinceDate, $systemDate));
    }

  function selectByPublicationAndArchiveDate($newsPublicationId, $sinceDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByPublicationAndArchiveDate($newsPublicationId, $sinceDate, $start, $rows)) {
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

    $header = $object->getHeader();
    $header = LibString::databaseEscapeQuotes($header);
    $footer = $object->getFooter();
    $footer = LibString::databaseEscapeQuotes($footer);

    return($this->dao->insert($object->getTitle(), $object->getImage(), $header, $footer, $object->getReleaseDate(), $object->getArchive(), $object->getNotPublished(), $object->getNewsPublicationId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $header = $object->getHeader();
    $header = LibString::databaseEscapeQuotes($header);
    $footer = $object->getFooter();
    $footer = LibString::databaseEscapeQuotes($footer);

    return($this->dao->update($object->getId(), $object->getTitle(), $object->getImage(), $header, $footer, $object->getReleaseDate(), $object->getArchive(), $object->getNotPublished(), $object->getNewsPublicationId()));
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
