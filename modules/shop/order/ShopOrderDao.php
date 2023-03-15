<?php

class ShopOrderDao extends Dao {

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
firstname varchar(255) not null,
lastname varchar(255) not null,
organisation varchar(255),
vat_number varchar(50),
email varchar(255) not null,
telephone varchar(20),
mobile_phone varchar(20),
fax varchar(20),
message text,
handling_fee int unsigned,
discount_code varchar(12),
discount_amount varchar(10),
currency varchar(3) not null,
invoice_number varchar(50),
invoice_note varchar(1024),
invoice_language_code varchar(2),
invoice_address_id int unsigned not null,
index (invoice_address_id), foreign key (invoice_address_id) references address(id),
shipping_address_id int unsigned,
index (shipping_address_id), foreign key (shipping_address_id) references address(id),
order_date datetime not null,
due_date datetime not null,
client_ip varchar(20) not null,
status varchar(10) not null,
payment_type varchar(10) not null,
payment_transaction_id varchar(50),
user_account_id int unsigned,
index (user_account_id), foreign key (user_account_id) references user(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($firstname, $lastname, $organisation, $vatNumber, $email, $telephone, $mobilePhone, $fax, $message, $handlingFee, $discountCode, $discountAmount, $currency, $invoiceNumber, $invoiceNote, $invoiceLanguage, $invoiceAddressId, $shippingAddressId, $orderDate, $dueDate, $clientIP, $status, $paymentType, $paymentTransactionID, $userId) {
    $userId = LibString::emptyToNULL($userId);
    $shippingAddressId = LibString::emptyToNULL($shippingAddressId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$firstname', '$lastname', '$organisation', '$vatNumber', '$email', '$telephone', '$mobilePhone', '$fax', '$message', '$handlingFee', '$discountCode', '$discountAmount', '$currency', '$invoiceNumber', '$invoiceNote', '$invoiceLanguage', '$invoiceAddressId', $shippingAddressId, '$orderDate', '$dueDate', '$clientIP', '$status', '$paymentType', '$paymentTransactionID', $userId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $firstname, $lastname, $organisation, $vatNumber, $email, $telephone, $mobilePhone, $fax, $message, $handlingFee, $discountCode, $discountAmount, $currency, $invoiceNumber, $invoiceNote, $invoiceLanguage, $invoiceAddressId, $shippingAddressId, $orderDate, $dueDate, $clientIP, $status, $paymentType, $paymentTransactionID, $userId) {
    $userId = LibString::emptyToNULL($userId);
    $shippingAddressId = LibString::emptyToNULL($shippingAddressId);
    $sqlStatement = "UPDATE $this->tableName SET firstname = '$firstname', lastname = '$lastname', organisation = '$organisation', vat_number = '$vatNumber', email = '$email', telephone = '$telephone', mobile_phone = '$mobilePhone', fax = '$fax', message = '$message', handling_fee = '$handlingFee', discount_code = '$discountCode', discount_amount = '$discountAmount', currency = '$currency', invoice_number = '$invoiceNumber', invoice_note = '$invoiceNote', invoice_language_code = '$invoiceLanguage', invoice_address_id = '$invoiceAddressId', shipping_address_id = $shippingAddressId, order_date = '$orderDate', due_date = '$dueDate', client_ip = '$clientIP', status = '$status', payment_type = '$paymentType', payment_transaction_id = '$paymentTransactionID', user_account_id = $userId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY order_date DESC";
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

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' ORDER BY order_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' ORDER BY order_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR lower(email) LIKE lower('%$searchPattern%') OR lower(organisation) LIKE lower('%$searchPattern%') OR telephone LIKE '%$searchPattern%' OR mobile_phone LIKE '%$searchPattern%' OR fax LIKE '%$searchPattern%' OR lower(message) LIKE lower('%$searchPattern%') ORDER BY order_date DESC";
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

  function selectByStatus($status, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE status = '$status' ORDER BY order_date DESC";
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

  function selectByYearAndMonth($year, $month) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE YEAR(order_date) = '$year' AND MONTH(order_date) = '$month' ORDER BY order_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByStatusAndYearAndMonth($status, $year, $month) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE status = '$status' AND YEAR(order_date) = '$year' AND MONTH(order_date) = '$month' ORDER BY order_date DESC";
    return($this->querySelect($sqlStatement));
  }

}

?>
