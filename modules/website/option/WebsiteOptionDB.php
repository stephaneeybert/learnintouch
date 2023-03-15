<?

class WebsiteOptionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function __construct() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->tableName = DB_TABLE_WEBSITE_OPTION;

    $this->dao = new WebsiteOptionDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function selectById($id) {
    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new WebsiteOption();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setValue($row['value']);
        $object->setWebsiteId($row['website_id']);
        return($object);
      }
    }
  }

  function selectByNameAndWebsiteId($name, $websiteId) {
    if ($result = $this->dao->selectByNameAndWebsiteId($name, $websiteId)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new WebsiteOption();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setValue($row['value']);
        $object->setWebsiteId($row['website_id']);
        return($object);
      }
    }
  }

  function selectByWebsiteId($websiteId) {
    $objects = Array();
    if ($result = $this->dao->selectByWebsiteId($websiteId)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);

        $object = new WebsiteOption();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setValue($row['value']);
        $object->setWebsiteId($row['website_id']);

        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function insert($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->insert($object->getName(), $object->getValue(), $object->getWebsiteId()));
  }

  function update($object) {
    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getName(), $object->getValue(), $object->getWebsiteId()));
  }

  function delete($id) {
    return($this->dao->delete($id));
  }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
  }

}

?>
