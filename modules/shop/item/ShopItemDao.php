<?php

class ShopItemDao extends Dao {

  var $tableName;

  function __construct($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(50) not null,
short_description varchar(255),
long_description text,
reference varchar(30),
weight varchar(3),
price varchar(12),
vat_rate varchar(5),
shipping_fee varchar(10),
category_id int unsigned,
index (category_id), foreign key (category_id) references shop_category(id),
url varchar(255),
list_order int unsigned not null,
hide boolean not null,
added datetime not null,
last_modified datetime not null,
available datetime not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $shortDescription, $longDescription, $reference, $weight, $price, $vatRate, $shippingFee, $categoryId, $url, $listOrder, $hide, $added, $lastModified, $available) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$shortDescription', '$longDescription', '$reference', '$weight', '$price', '$vatRate', '$shippingFee', $categoryId, '$url', '$listOrder', '$hide', '$added', '$lastModified', '$available')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $shortDescription, $longDescription, $reference, $weight, $price, $vatRate, $shippingFee, $categoryId, $url, $listOrder, $hide, $added, $lastModified, $available) {
    $categoryId = LibString::emptyToNULL($categoryId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', short_description = '$shortDescription', long_description = '$longDescription', reference = '$reference', weight = '$weight', price = '$price', vat_rate = '$vatRate', shipping_fee = '$shippingFee', category_id = $categoryId, url = '$url', list_order = '$listOrder', hide = '$hide', added = '$added', last_modified = '$lastModified', available = '$available' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(name) LIKE lower('%$searchPattern%') OR lower(short_description) LIKE lower('%$searchPattern%') OR lower(long_description) LIKE lower('%$searchPattern%') OR lower(reference) LIKE lower('%$searchPattern%') ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY list_order";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByCategoryIdOrderById($categoryId) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1') ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($categoryId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($categoryId) {
    $sqlStatement = "SELECT count(distinct si1.id) as count FROM $this->tableName si1, $this->tableName si2 where si1.id != si2.id and si1.category_id = si2.category_id and si1.list_order = si2.list_order and si1.category_id = $categoryId";
    return($this->querySelect($sqlStatement));
  }

}

?>
