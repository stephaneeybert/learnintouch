<?PHP

// Name of the database table(s)
define('DB_TABLE_USER', "user_account");

// The length of a password created for the user
define('USER_NEW_PASSWORD_LENGTH', 6);
define('USER_PASSWORD_SALT_LENGTH', 30);

define('USER_TOKEN_NAME', "user_login");

// The valid until cases for the user accounts
define('USER_ACCOUNT_NOT_VALID', 1);
define('USER_ACCOUNT_VALID_TEMPORARILY', 2);
define('USER_ACCOUNT_VALID_PERMANENTLY', 3);
define('USER_ACCOUNT_EMAIL_UNCONFIRMED', 4);

// The last time the user has logged in
define('USER_LAST_LOGIN_WEEK', 1);
define('USER_LAST_LOGIN_MONTH', 2);
define('USER_LAST_LOGIN_QUARTER', 3);
define('USER_LAST_LOGIN_SEMESTER', 4);
define('USER_LAST_LOGIN_YEAR', 5);
define('USER_LAST_LOGIN_MORE', 6);

// The session variable
define('USER_SESSION_VALIDITY', "admin_user_validity");
define('USER_SESSION_LAST_LOGIN', "admin_user_last_login");
define('USER_SESSION_MAIL_LIST_SUBSCRIPTION', "admin_user_mail_list_subscription");
define('USER_SESSION_SMS_LIST_SUBSCRIPTION', "admin_user_sms_list_subscription");
define('USER_SESSION_SEARCH_PATTERN', "admin_user_search_pattern");
define('USER_SESSION_LOGIN', "admin_user_login");
define('USER_SESSION_SESSION_TIME', "admin_user_session_time");

// Some variables
define('USER_MAIL_SUBSCRIBE', 1);
define('USER_MAIL_UNSUBSCRIBE', 2);
define('USER_SMS_SUBSCRIBE', 3);
define('USER_SMS_UNSUBSCRIBE', 4);

?>
