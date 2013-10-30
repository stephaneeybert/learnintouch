<?

class NewsFeedDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function NewsFeedDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_NEWS_FEED;

    $this->dao = new NewsFeedDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new NewsFeed();
      $object->setId($row['id']);
      $object->setNewsPaperId($row['news_paper_id']);
      $object->setImage($row['image']);
      $object->setMaxDisplayNumber($row['max_display_number']);
      $object->setImageAlign($row['image_align']);
      $object->setImageWidth($row['image_width']);
      $object->setWithExcerpt($row['with_excerpt']);
      $object->setWithImage($row['with_image']);
      $object->setSearchOptions($row['search_options']);
      $object->setSearchCalendar($row['search_calendar']);
      $object->setDisplayUpcoming($row['display_upcoming']);
      $object->setSearchTitle($row['search_title']);
      $object->setSearchDisplayAsPage($row['search_display_as_page']);

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

  function selectByNewsPaperId($newsPaperId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNewsPaperId($newsPaperId)) {
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

    return($this->dao->insert($object->getNewsPaperId(), $object->getImage(), $object->getMaxDisplayNumber(), $object->getImageAlign(), $object->getImageWidth(), $object->getWithExcerpt(), $object->getWithImage(), $object->getSearchOptions(), $object->getSearchCalendar(), $object->getDisplayUpcoming(), $object->getSearchTitle(), $object->getSearchDisplayAsPage()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getNewsPaperId(), $object->getImage(), $object->getMaxDisplayNumber(), $object->getImageAlign(), $object->getImageWidth(), $object->getWithExcerpt(), $object->getWithImage(), $object->getSearchOptions(), $object->getSearchCalendar(), $object->getDisplayUpcoming(), $object->getSearchTitle(), $object->getSearchDisplayAsPage()));
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
