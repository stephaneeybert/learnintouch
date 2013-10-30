<?PHP

// Names of the database tables
define('DB_TABLE_DYNPAGE', "webpage");
define('DB_TABLE_DYNPAGE_NAVMENU', "webpage_navmenu");

// Prefix of the name of a duplicated page
define('DYNPAGE_DUPLICATA', '_DUPLICATA');

// Prefix of the name of a page put into the garbage
define('DYNPAGE_GARBAGE', '_GARBAGE');

// The id of the root directory
// This is not a table record id, but only a php variable id
// This is to avoid php confusing the empty table record root id for an unset variable
define('DYNPAGE_ROOT_ID', '-1');

// Display states
define('DYNPAGE_COLLAPSED', 1);
define('DYNPAGE_FOLDED', 2);

// The session variable
define('DYNPAGE_SESSION_DISPLAY', "admin_webpage_display");
define('DYNPAGE_SESSION_USER_PAGE', "admin_webpage_user_page");

?>
