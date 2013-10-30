<?php

define('DB_NAME_PREFIX', "db_");

// Names for the host, the database, users and tables
if (isLocalhost()) {
  // The database used by the engine and common to all websites
  define('DB_COMMON_HOST', "localhost");
  define('DB_SYSTEM_DB_NAME', "mysql");
  define('DB_COMMON_USER', "engine");
  define('DB_COMMON_PASS', "mignet");
  define('DB_COMMON_DB_NAME', "db_engine");
} else {
  // The database used by the engine and common to all websites
  define('DB_COMMON_HOST', "localhost");
  define('DB_SYSTEM_DB_NAME', "mysql");
  define('DB_COMMON_USER', "engine");
  define('DB_COMMON_PASS', "mignet");
  define('DB_COMMON_DB_NAME', "db_engine");
}

?>
