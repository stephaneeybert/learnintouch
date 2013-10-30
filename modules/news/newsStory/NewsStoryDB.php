<?

class NewsStoryDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsStoryDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NEWS_STORY;

    $this->dao = new NewsStoryDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsStory();
      $object->setId($row['id']);
      $object->setHeadline($row['headline']);
      $object->setExcerpt($row['excerpt']);
      $object->setAudio($row['audio']);
      $object->setAudioUrl($row['audio_url']);
      $object->setLink($row['link']);
      $object->setReleaseDate($row['release_date']);
      $object->setArchive($row['archive_date']);
      $object->setEventStartDate($row['event_start_date']);
      $object->setEventEndDate($row['event_end_date']);
      $object->setNewsEditor($row['news_editor_id']);
      $object->setNewsPaper($row['news_paper_id']);
      $object->setNewsHeading($row['news_heading_id']);
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

  function selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($newsPaperId, $newsHeadingId) {
    if ($this->countDuplicateListOrderRows($newsPaperId, $newsHeadingId) > 0) {
      if ($newsStories = $this->selectByNewsPaperAndNewsHeadingOrderById($newsPaperId, $newsHeadingId)) {
        if (count($newsStories) > 0) {
          $listOrder = 0;
          foreach ($newsStories as $newsStory) {
            $listOrder = $listOrder + 1;
            $newsStory->setListOrder($listOrder);
            $this->update($newsStory);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($newsPaperId, $newsHeadingId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($newsPaperId, $newsHeadingId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  function selectByNewsEditor($newsEditorId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsEditor($newsEditorId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaper($newsPaperId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaper($newsPaperId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsHeading($newsHeadingId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsHeading($newsHeadingId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndNewsEditor($newsPaperId, $newsEditorId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndNewsEditor($newsPaperId, $newsEditorId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndNewsHeadingOrderById($newsPaperId, $newsHeadingId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndNewsHeadingOrderById($newsPaperId, $newsHeadingId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsHeadingAndNewsEditor($newsHeadingId, $newsEditorId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsHeadingAndNewsEditor($newsHeadingId, $newsEditorId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndPublished($newsPaperId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndPublished($newsPaperId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndArchived($newsPaperId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndArchived($newsPaperId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndDeferred($newsPaperId, $systemDate, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndDeferred($newsPaperId, $systemDate, $start, $rows)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByNewsPaperAndNewsHeadingAndNewsEditor($newsPaperId, $newsHeadingId, $newsEditorId, $start = false, $rows = false) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByNewsPaperAndNewsHeadingAndNewsEditor($newsPaperId, $newsHeadingId, $newsEditorId, $start, $rows)) {
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $excerpt = $object->getExcerpt();
    $excerpt = LibString::databaseEscapeQuotes($excerpt);

    return($this->dao->insert($object->getHeadline(), $excerpt, $object->getAudio(), $object->getAudioUrl(), $object->getLink(), $object->getReleaseDate(), $object->getArchive(), $object->getEventStartDate(), $object->getEventEndDate(), $object->getNewsEditor(), $object->getNewsPaper(), $object->getNewsHeading(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    $excerpt = $object->getExcerpt();
    $excerpt = LibString::databaseEscapeQuotes($excerpt);

    return($this->dao->update($object->getId(), $object->getHeadline(), $excerpt, $object->getAudio(), $object->getAudioUrl(), $object->getLink(), $object->getReleaseDate(), $object->getArchive(), $object->getEventStartDate(), $object->getEventEndDate(), $object->getNewsEditor(), $object->getNewsPaper(), $object->getNewsHeading(), $object->getListOrder()));
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
