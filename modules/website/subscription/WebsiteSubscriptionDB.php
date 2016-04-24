<?

class WebsiteSubscriptionDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function WebsiteSubscriptionDB() {
    global $gSqlCommonDataSource;

    $this->dataSource = $gSqlCommonDataSource;

    $this->tableName = DB_TABLE_WEBSITE_SUBSCRIPTION;

    $this->dao = new WebsiteSubscriptionDao($this->dataSource, $this->tableName);
    }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
    }

  function selectById($id) {
    if ($result = $this->dao->selectById($id)) {
      if ($result->getRowCount() == 1) {
        $row = $result->getRow(0);

        $object = new WebsiteSubscription();
        $object->setId($row['id']);
        $object->setOpeningDate($row['opening_date']);
        $object->setFee($row['fee']);
        $object->setDuration($row['duration']);
        $object->setAutoRenewal($row['auto_renewal']);
        $object->setTerminationDate($row['termination_date']);
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

        $object = new WebsiteSubscription();
        $object->setId($row['id']);
        $object->setOpeningDate($row['opening_date']);
        $object->setFee($row['fee']);
        $object->setDuration($row['duration']);
        $object->setAutoRenewal($row['auto_renewal']);
        $object->setTerminationDate($row['termination_date']);
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

    return($this->dao->insert($object->getOpeningDate(), $object->getFee(), $object->getDuration(), $object->getAutoRenewal(), $object->getTerminationDate(), $object->getWebsiteId()));
    }

  function update($object) {
    if (!$object) {
      return(false);
      }

    return($this->dao->update($object->getId(), $object->getOpeningDate(), $object->getFee(), $object->getDuration(), $object->getAutoRenewal(), $object->getTerminationDate(), $object->getWebsiteId()));
    }

  function delete($id) {
    return($this->dao->delete($id));
    }

  function getLastInsertId() {
    return($this->dataSource->getLastInsertId());
    }

  }

?>
