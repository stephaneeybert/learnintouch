<?

class NewsStoryParagraphDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsStoryParagraphDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NEWS_STORY_PARAGRAPH;

    $this->dao = new NewsStoryParagraphDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsStoryParagraph();
      $object->setId($row['id']);
      $object->setHeader($row['header']);
      $object->setBody($row['body']);
      $object->setFooter($row['footer']);
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $header = $object->getHeader();
    $header = LibString::databaseEscapeQuotes($header);
    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);
    $footer = $object->getFooter();
    $footer = LibString::databaseEscapeQuotes($footer);

    return($this->dao->insert($header, $body, $footer, $object->getNewsStoryId()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    $header = $object->getHeader();
    $header = LibString::databaseEscapeQuotes($header);
    $body = $object->getBody();
    $body = LibString::databaseEscapeQuotes($body);
    $footer = $object->getFooter();
    $footer = LibString::databaseEscapeQuotes($footer);

    return($this->dao->update($object->getId(), $header, $body, $footer, $object->getNewsStoryId()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
