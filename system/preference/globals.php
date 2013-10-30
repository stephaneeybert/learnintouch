<?PHP

// Name of the database table(s)
define('DB_TABLE_PREFERENCE', "preference");

// Types of preferences
// These are used in the database so do not modify them unless also updating their database values
define('PREFERENCE_TYPE_BOOLEAN', 1);
define('PREFERENCE_TYPE_TEXT', 2);
define('PREFERENCE_TYPE_MLTEXT', 3);
define('PREFERENCE_TYPE_TEXTAREA', 4);
define('PREFERENCE_TYPE_RAW_CONTENT', 5);
define('PREFERENCE_TYPE_SELECT', 6);
define('PREFERENCE_TYPE_RANGE', 7);
define('PREFERENCE_TYPE_COLOR', 8);
define('PREFERENCE_TYPE_URL', 9);

define('PREFERENCE_ID', 'preference_id_');

?>
