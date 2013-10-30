<?PHP

// Module identifiers
$i = 1;
define('OPTION_NEWS', $i++);
define('OPTION_PEOPLE', $i++);
define('OPTION_FLASH', $i++);
define('OPTION_DOCUMENT', $i++);
define('OPTION_USER', $i++);
define('OPTION_SECURED_PAGES', $i++);
define('OPTION_GUESTBOOK', $i++);
define('OPTION_STATISTICS', $i++);
define('OPTION_MAIL', $i++);
define('OPTION_SMS', $i++);
define('OPTION_PHONE_MODEL', $i++);
define('OPTION_ELEARNING', $i++);
define('OPTION_ELEARNING_STORE', $i++);
define('OPTION_ELEARNING_EXPORT', $i++);
define('OPTION_SHOP', $i++);
define('OPTION_LANGUAGE_TRANSLATE', $i++);
define('OPTION_AFFILIATE', $i++);

// The options are used to grant usage permissions
// These array values are used in a database table so do not modify them here!
// An option simply is a pointer to a module
$gWebsiteOptions = array(
  OPTION_NEWS => array('OPTION_NEWS', 'MODULE_NEWS', ''),
  OPTION_PEOPLE => array('OPTION_PEOPLE', 'MODULE_PEOPLE', ''),
  OPTION_FLASH => array('OPTION_FLASH', 'MODULE_FLASH', ''),
  OPTION_DOCUMENT => array('OPTION_DOCUMENT', 'MODULE_DOCUMENT', ''),
  OPTION_USER => array('OPTION_USER', 'MODULE_USER', ''),
  OPTION_SECURED_PAGES => array('OPTION_SECURED_PAGES', 'MODULE_SECURED_PAGES', ''),
  OPTION_GUESTBOOK => array('OPTION_GUESTBOOK', 'MODULE_GUESTBOOK', ''),
  OPTION_STATISTICS => array('OPTION_STATISTICS', 'MODULE_STATISTICS', ''),
  OPTION_MAIL => array('OPTION_MAIL', 'MODULE_MAIL', ''),
  OPTION_SMS => array('OPTION_SMS', 'MODULE_SMS', ''),
  OPTION_PHONE_MODEL => array('OPTION_PHONE_MODEL', 'MODULE_PHONE_MODEL', ''),
  OPTION_ELEARNING => array('OPTION_ELEARNING', 'MODULE_ELEARNING', ''),
  OPTION_ELEARNING_STORE => array('OPTION_ELEARNING_STORE', 'MODULE_ELEARNING_STORE', ''),
  OPTION_ELEARNING_EXPORT => array('OPTION_ELEARNING_EXPORT', 'MODULE_ELEARNING_EXPORT', ''),
  OPTION_SHOP => array('OPTION_SHOP', 'MODULE_SHOP', ''),
  OPTION_LANGUAGE_TRANSLATE => array('OPTION_LANGUAGE_TRANSLATE', 'MODULE_LANGUAGE_TRANSLATE', array('se', 'no', 'dk', 'fi', 'de', 'es', 'it', 'en', 'fr')),
  OPTION_AFFILIATE => array('OPTION_AFFILIATE', 'MODULE_AFFILIATE', ''),
);

?>
