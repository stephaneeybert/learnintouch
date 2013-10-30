<?PHP

// Name of the database table(s)
define('DB_TABLE_SMS', "sms");
define('DB_TABLE_SMS_CATEGORY', "sms_category");
define('DB_TABLE_SMS_NUMBER', "sms_number");
define('DB_TABLE_SMS_NUMBER_IMPORT', "sms_number_import");
define('DB_TABLE_SMS_LIST', "sms_list");
define('DB_TABLE_SMS_LIST_USER', "sms_list_user");
define('DB_TABLE_SMS_LIST_NUMBER', "sms_list_number");
define('DB_TABLE_SMS_HISTORY', "sms_history");
define('DB_TABLE_SMS_OUTBOX', "sms_outbox");

// The meta names
define('SMS_META_USER_FIRSTNAME', '[USER_FIRSTNAME]');
define('SMS_META_USER_LASTNAME', '[USER_LASTNAME]');
define('SMS_META_USER_MOBILE_PHONE', '[USER_MOBILE_PHONE]');
define('SMS_META_USER_PASSWORD', '[USER_PASSWORD]');

// Display states
define('SMS_FAILED', 1);
define('SMS_SENT', 2);
define('SMS_ALL', 3);

// The gateway providers
define('SMS_GATEWAY_TM4B', 1);

// The international phone code for France
define('SMS_FRANCE_PREFIX', 33);

// The maximum length (number of characters) of an SMS message
define('SMS_MESSAGE_LENGTH', 160);

// The default route cost (a high cost of 10 cents of a euro, the normal cost should be cheaper)
define('SMS_DEFAULT_ROUTE_COST', 0.10);

// The url to the csv file containing the delivery rates for sending an sms
define('SMS_TM4B_RATES_FILE_URL', 'http://www.tm4b.com/downloads/csv.delivery-rates.php');
define('SMS_TM4B_RATES_FILE_SEPARATOR', ',');

// The session variable
define('SMS_SESSION_CATEGORY', "admin_sms_category");
define('SMS_SESSION_SEARCH_PATTERN', "admin_sms_search_pattern");
define('SMS_SESSION_SUBSCRIBE', "admin_sms_subscribe");
define('SMS_SESSION_STATUS', "admin_sms_status");

// Some variables
define('SMS_SUBSCRIBE', 1);
define('SMS_UNSUBSCRIBE', 2);

?>
