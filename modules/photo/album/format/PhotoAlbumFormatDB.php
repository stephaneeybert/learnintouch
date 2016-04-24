<?

class PhotoAlbumFormatDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function PhotoAlbumFormatDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_PHOTO_ALBUM_FORMAT;

    $this->dao = new PhotoAlbumFormatDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new PhotoAlbumFormat();
      $object->setId($row['id']);
      $object->setPhotoAlbumId($row['photo_album_id']);
      $object->setPhotoFormatId($row['photo_format_id']);
      $object->setPrice($row['price']);

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

  function selectByPhotoFormatIdAndPhotoAlbumId($photoFormatId, $photoAlbumId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPhotoFormatIdAndPhotoAlbumId($photoFormatId, $photoAlbumId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);
        $object = $this->getObject($row);
        return($object);
        }
      }
    }

  function selectByPhotoFormatId($photoFormatId) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByPhotoFormatId($photoFormatId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
        }
      }
    }

  function selectByPhotoAlbumId($photoAlbumId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByPhotoAlbumId($photoAlbumId)) {
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

    return($this->dao->insert($object->getPhotoAlbumId(), $object->getPhotoFormatId(), $object->getPrice()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getPhotoAlbumId(), $object->getPhotoFormatId(), $object->getPrice()));
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
