<?

class TemplatePageTagDB {

  var $dataSource;
  var $tableName;
  var $dao;

  function TemplatePageTagDB() {
    global $gSqlDataSource;

    $this->dataSource = $gSqlDataSource;

    $this->tableName = DB_TABLE_TEMPLATE_PAGE_TAG;

    $this->dao = new TemplatePageTagDao($this->dataSource, $this->tableName);
  }

  function createTable() {
    $this->dataSource->selectDatabase();

    return($this->dao->createTable());
  }

  function getObject($row) {
    if ($row && is_array($row)) {
      $object = new TemplatePageTag();
      $object->setId($row['id']);
      $object->setTemplatePageId($row['template_page_id']);
      $object->setTemplatePropertySetId($row['template_property_set_id']);
      $object->setTagID($row['dom_tag_id']);

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

  // There should ideally be only one row for a tagID but it may happen
  // after some styling that more than one row exists for a tagID
  function selectByTemplatePageIdAndTagID($templatePageId, $tagID) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplatePageIdAndTagID($templatePageId, $tagID)) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $object = $this->getObject($row);
        $objects[$i] = $object;
      }
    }

    return($objects);
  }

  function selectByTemplatePageId($templatePageId) {
    $this->dataSource->selectDatabase();

    $objects = Array();
    if ($result = $this->dao->selectByTemplatePageId($templatePageId)) {
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

    return($this->dao->insert($object->getTemplatePageId(), $object->getTemplatePropertySetId(), $object->getTagID()));
  }

  function update($object) {
    $this->dataSource->selectDatabase();

    if (!$object) {
      return(false);
    }

    return($this->dao->update($object->getId(), $object->getTemplatePageId(), $object->getTemplatePropertySetId(), $object->getTagID()));
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
