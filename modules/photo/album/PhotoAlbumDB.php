<?

class PhotoAlbumDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function PhotoAlbumDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_PHOTO_ALBUM;

    $this->dao = new PhotoAlbumDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new PhotoAlbum();
      $object->setId($row['id']);
      $object->setName($row['name']);
      $object->setFolderName($row['folder_name']);
      $object->setEvent($row['event']);
      $object->setLocation($row['location']);
      $object->setPublicationDate($row['publication_date']);
      $object->setPrice($row['price']);
      $object->setHide($row['hide']);
      $object->setNoSlideShow($row['no_slide_show']);
      $object->setNoZoom($row['no_zoom']);
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

  function selectByFolderName($folderName) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByFolderName($folderName)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByNextListOrder($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByNextListOrder($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByPreviousListOrder($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPreviousListOrder($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
      }
    }
  }

  function selectByListOrder($listOrder) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByListOrder($listOrder)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder() {
    if ($this->countDuplicateListOrderRows() > 0) {
      if ($photoAlbums = $this->selectAllOrderById()) {
        if (count($photoAlbums) > 0) {
          $listOrder = 0;
          foreach ($photoAlbums as $photoAlbum) {
            $listOrder = $listOrder + 1;
            $photoAlbum->setListOrder($listOrder);
            $this->update($photoAlbum);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows() {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows();

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
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

  function selectNotHidden() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectNotHidden()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectAllOrderById() {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectAllOrderById()) {
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

  function insert($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getName(), $object->getFolderName(), $object->getEvent(), $object->getLocation(), $object->getPublicationDate(), $object->getPrice(), $object->getHide(), $object->getNoSlideShow(), $object->getNoZoom(), $object->getListOrder()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getFolderName(), $object->getEvent(), $object->getLocation(), $object->getPublicationDate(), $object->getPrice(), $object->getHide(), $object->getNoSlideShow(), $object->getNoZoom(), $object->getListOrder()));
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
