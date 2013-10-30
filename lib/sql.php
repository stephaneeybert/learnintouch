<?PHP

// MySql specific values
define ("DB_ERROR_NOT_SUPPORTED", -1);

class LibSql {

  // Connect to the database server and select a database
  static function dbConnection() {
    // Connect to the database server
    $db = LibSql::dbConnect();
    // Select a database
    LibSql::dbSelectDatabase($db);
    }

  // Get a null value from the sql 0 value
  // Transform the sql value 0 into a PHP empty value
  // There is no such null value in sql
  // The zero is the pseudo null value for a numeric field
  static function dbTransformNull($value) {
    if ($value == 0) {
      $value = '';
      }
    return($value);
    }

  // Get the error number
  static function dbErrno() {
    return(mysql_errno());
    }

  // Get the error message
  static function dbError() {
    return(mysql_error());
    }

  // Connect to the database server
  static function dbConnect() {
    switch(func_num_args()) {
      case 0:
        $db = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        break;
      case 1:
        $db = mysql_connect($args[0]);
        break;
      case 2:
        $db = mysql_connect($args[0], $args[1]);
        break;
      case 3:
        $db = mysql_connect($args[0], $args[1], $args[2]);
        break;
      default:
        $db = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        break;
      }
    if (!$db) {
      print("Could not establish a connection with the database.<BR>" . LibSql::dbErrno() . " " . LibSql::dbError());
      } else {
      return($db);
      }
    }

  // Connect to the database server with a persistent connection
  static function dbPConnect() {
    switch(func_num_args()) {
      case 0:
        $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS);
        break;
      case 1:
        $db = mysql_pconnect($args[0]);
        break;
      case 2:
        $db = mysql_pconnect($args[0], $args[1]);
        break;
      case 3:
        $db = mysql_pconnect($args[0], $args[1], $args[2]);
        break;
      default:
        $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS);
        break;
      }
    if (!$db) {
      print("Could not establish a persistent connection with the database.<BR>" . LibSql::dbErrno() . " " . LibSql::dbError());
      } else {
      return($db);
      }
    }

  // Select a database
  static function dbSelectDatabase($db) {
    global $db_name;

    mysql_select_db($db_name, $db);
    }

  // Query the database
  static function &dbQuery() {
    switch(func_num_args()) {
      case 1:
        return(mysql_query(func_get_arg(0)));
      default:
        return(mysql_query(func_get_arg(0), func_get_arg(1)));
      }
    }

  // Fetch a row from the query result
  static function &dbFetchRow($result) {
    return(mysql_fetch_array($result));
    }

  // Get the number of rows from the query result
  static function dbNumRows($result) {
    return(mysql_num_rows($result));
    }

  // Close the database connection
  static function dbClose() {
    // I don't know why, but the close function often gives me a message...
    // Warning: 1 is not a valid MySQL-Link resource in .../mysql.php
    return;
    switch(func_num_args()) {
      case 0:
        return(mysql_close());
      default:
        return(mysql_close(func_get_arg(0)));
      }
    }

  // Reset the fetch index to a specific row
  static function dbRowSeek($result, $index) {
    return(mysql_data_seek($result, $index));
    }

  // Return the result set
  static function &dbResult($args = array()) {
    switch(count($args)) {
      case 2:
        return(mysql_result($args[0], $args[1]));
      default:
        return(mysql_result($args[0], $args[1], $args[2]));
      }
    }

  // Get the id of the last inserted row
  static function dbGetInsertId() {
    return mysql_insert_id();
    }

  // Get the list of table as a select result
  static function dbListTables($dbName) {
    return mysql_list_tables($dbName);
    }

  // ???
  static function dbFree($args = array()) {
    return mysql_free_query($args[0]);
    }

  // ???
  static function dbFieldSeek($args=array()) {
    return mysql_field_seek ($args[0], $args[1]);
    }

  // Get the number of fields from a result set
  static function dbNumFields($result) {
    return mysql_num_fields ($result);
    }

  // ???
  static function dbCommit($args=array()) {
    return ERROR_NOT_SUPPORTED;
    }

  // ???
  static function dbPrepare($args=array()) {
    return $args[0];
    }

  // ???
  static function dbExecute($args=array()) {
    $stmt     = $args[0];
    $prepArgs = $args[1];

    if (!is_array($prepArgs)) {
      return($stmt);
      }

    $parts = explode ('?', $stmt);
    foreach ($parts as $part) {
      $new_stmt .= $part . array_shift($prepArgs);
      }

    return($new_stmt);
    }

  }

?>
