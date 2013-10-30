<?PHP

// Name of the database table(s)
define('DB_TABLE_CONTENT_IMPORT', "content_import");
define('DB_TABLE_CONTENT_IMPORT_HISTORY', "content_import_history");

// Some error codes
define('CONTENT_IMPORT_ERROR_ALREADY_EXISTS', 'content_import_error_already_exists');
define('CONTENT_IMPORT_ERROR_IMPORTER_UNKNOWN_WEBSITE', 'content_import_import_unknown_website');
define('CONTENT_IMPORT_ERROR_IMPORTER_PENDING', 'content_import_import_pending');
define('CONTENT_IMPORT_ERROR_IMPORTER_DENIED', 'content_import_import_denied');
define('CONTENT_IMPORT_ERROR_IMPORTER_GRANTED', 'content_import_import_granted');
define('CONTENT_IMPORT_ERROR_IMPORTER_UNKNOWN_KEY', 'content_import_import_unknown_key');

define('CONTENT_IMPORT_TOKEN_NAME', 'content_import');

// These values are used in the database - Do not change them
define('CONTENT_IMPORT_PERMISSION_PENDING', 'pending');
define('CONTENT_IMPORT_PERMISSION_DENIED', 'denied');

// The length of a permission key
define('CONTENT_IMPORT_KEY_LENGTH', 10);

// The session variable
define('CONTENT_IMPORT_SESSION_CURRENT', "admin_content_import_current");
define('CONTENT_IMPORT_SESSION_SEARCH_PATTERN', "admin_content_import_search_pattern");

?>
