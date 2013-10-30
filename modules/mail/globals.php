<?PHP

// Name of the database table(s)
define('DB_TABLE_MAIL', "mail");
define('DB_TABLE_MAIL_CATEGORY', "mail_category");
define('DB_TABLE_MAIL_ADDRESS', "mail_address");
define('DB_TABLE_MAIL_ADDRESS_IMPORT', "mail_address_import");
define('DB_TABLE_MAIL_LIST', "mail_list");
define('DB_TABLE_MAIL_LIST_USER', "mail_list_user");
define('DB_TABLE_MAIL_LIST_ADDRESS', "mail_list_address");
define('DB_TABLE_MAIL_HISTORY', "mail_history");
define('DB_TABLE_MAIL_OUTBOX', "mail_outbox");

// The meta names
define('MAIL_META_USER_FIRSTNAME', '[USER_FIRSTNAME]');
define('MAIL_META_USER_LASTNAME', '[USER_LASTNAME]');
define('MAIL_META_USER_EMAIL', '[USER_EMAIL]');
define('MAIL_META_USER_LOGIN', '[USER_LOGIN]');
define('MAIL_META_USER_PASSWORD', '[USER_PASSWORD]');
define('MAIL_META_ELEARNING_NEXT_EXERCISE_NAME', '[MAIL_META_ELEARNING_NEXT_EXERCISE_NAME]');

// Display states
define('MAIL_FAILED', 1);
define('MAIL_SENT', 2);
define('MAIL_ALL', 3);

// Prefix of the name of a duplicated mail
define('MAIL_DUPLICATA', '_DUPLICATA');

// The separators used to concatenate several meta names
define('MAIL_META_NAME_SEPARATOR', '||');
define('MAIL_META_NAME_VALUE_SEPARATOR', '|');

// The session variable
define('MAIL_SESSION_MAIL', "admin_mail_mail");
define('MAIL_SESSION_CATEGORY', "admin_mail_category");
define('MAIL_SESSION_SEARCH_PATTERN', "admin_mail_search_pattern");
define('MAIL_SESSION_SEARCH_COUNTRY', "admin_mail_search_country");
define('MAIL_SESSION_LIST_SEARCH_PATTERN', "admin_mail_list_search_pattern");
define('MAIL_SESSION_ADMIN_SEARCH_PATTERN', "admin_mail_admin_search_pattern");
define('MAIL_SESSION_STATUS', "admin_mail_status");

?>
