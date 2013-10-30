<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// The module names
// These modules are used to grant usage permissions
// These are used in the database so do not modify them unless also updating their database values
$gModules = array(
  MODULE_PROFILE => array('MODULE_PROFILE', $mlText[0]),
  MODULE_LANGUAGE => array('MODULE_LANGUAGE', $mlText[1]),
  MODULE_LANGUAGE_TRANSLATE => array('MODULE_LANGUAGE_TRANSLATE', $mlText[2]),
  MODULE_TEMPLATE => array('MODULE_TEMPLATE', $mlText[3]),
  MODULE_PHONE_MODEL => array('MODULE_PHONE_MODEL', $mlText[25]),
  MODULE_CLOCK => array('MODULE_CLOCK', $mlText[21]),
  MODULE_FLASH => array('MODULE_FLASH', $mlText[19]),
  MODULE_BACKUP => array('MODULE_BACKUP', $mlText[5]),
  MODULE_CLIENT => array('MODULE_CLIENT', $mlText[7]),
  MODULE_DYNPAGE => array('MODULE_DYNPAGE', $mlText[8]),
  MODULE_FORM => array('MODULE_FORM', $mlText[29]),
  MODULE_PEOPLE => array('MODULE_PEOPLE', $mlText[9]),
  MODULE_GUESTBOOK => array('MODULE_GUESTBOOK', $mlText[10]),
  MODULE_LINK => array('MODULE_LINK', $mlText[11]),
  MODULE_CONTACT => array('MODULE_CONTACT', $mlText[12]),
  MODULE_MAIL => array('MODULE_MAIL', $mlText[13]),
  MODULE_SMS => array('MODULE_SMS', $mlText[15]),
  MODULE_NEWS => array('MODULE_NEWS', $mlText[14]),
  MODULE_PHOTO => array('MODULE_PHOTO', $mlText[16]),
  MODULE_USER => array('MODULE_USER', $mlText[17]),
  MODULE_SECURED_PAGES => array('MODULE_SECURED_PAGES', $mlText[26]),
  MODULE_DOCUMENT => array('MODULE_DOCUMENT', $mlText[22]),
  MODULE_STATISTICS => array('MODULE_STATISTICS', $mlText[20]),
  MODULE_ELEARNING => array('MODULE_ELEARNING', $mlText[6]),
  MODULE_ELEARNING_STORE => array('MODULE_ELEARNING_STORE', $mlText[23]),
  MODULE_ELEARNING_EXPORT => array('MODULE_ELEARNING_EXPORT', $mlText[4]),
  MODULE_SHOP => array('MODULE_SHOP', $mlText[28]),
  MODULE_AFFILIATE => array('MODULE_AFFILIATE', $mlText[24]),
);

?>
