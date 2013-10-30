<?

class StatisticsRefererDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function StatisticsRefererDB() {
    $this->dataSource = Sql::initDataSource();

    $this->tableName = DB_TABLE_STATISTICS_REFERER;

    $this->dao = new StatisticsRefererDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function selectById($id) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new StatisticsReferer();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setDescription($row['description']);
        $object->setUrl($row['url']);
        return($object);
        }
      }
    }

  function selectByName($name) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByName($name)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new StatisticsReferer();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setDescription($row['description']);
        $object->setUrl($row['url']);
        return($object);
        }
      }
    }

  function selectByUrl($url) {
    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByUrl($url)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new StatisticsReferer();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setDescription($row['description']);
        $object->setUrl($row['url']);
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

        $object = new StatisticsReferer();
        $object->setId($row['id']);
        $object->setName($row['name']);
        $object->setDescription($row['description']);
        $object->setUrl($row['url']);

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

    return($this->dao->insert($object->getName(), $object->getDescription(), $object->getUrl()));
    }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getName(), $object->getDescription(), $object->getUrl()));
    }

  function delete($id) {
    $this->dataSource->selectDatabase();

    return($this->dao->delete($id));
    }

  }

?>
