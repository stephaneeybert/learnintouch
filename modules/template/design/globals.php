<?PHP

// Name of the database table(s)
define('DB_TABLE_TEMPLATE_MODEL', "template_model");
define('DB_TABLE_TEMPLATE_CONTAINER', "template_container");
define('DB_TABLE_TEMPLATE_STYLE', "template_style");
define('DB_TABLE_TEMPLATE_ELEMENT', "template_element");
define('DB_TABLE_TEMPLATE_ELEMENT_LANGUAGE', "template_element_language");
define('DB_TABLE_TEMPLATE_ELEMENT_TAG', "template_element_tag");
define('DB_TABLE_TEMPLATE_PAGE', "template_page");
define('DB_TABLE_TEMPLATE_PAGE_TAG', "template_page_tag");
define('DB_TABLE_TEMPLATE_PROPERTY_SET', "template_property_set");
define('DB_TABLE_TEMPLATE_PROPERTY', "template_property");

// Types of property
$i = 1;
define('TEMPLATE_PROPERTY_TYPE_BOOLEAN', $i++);
define('TEMPLATE_PROPERTY_TYPE_TEXT', $i++);
define('TEMPLATE_PROPERTY_TYPE_TEXTAREA', $i++);
define('TEMPLATE_PROPERTY_TYPE_SELECT', $i++);
define('TEMPLATE_PROPERTY_TYPE_RANGE', $i++);
define('TEMPLATE_PROPERTY_TYPE_COLOR', $i++);
define('TEMPLATE_PROPERTY_TYPE_IMAGE', $i++);

// Prefix of the name of a duplicated model
define('TEMPLATE_DUPLICATA', '_DUPLICATA');

// A separator
define('TEMPLATE_SEPARATOR', '::');

// The types of objects being serialized
$i = 1;
define('TEMPLATE_MODEL', "templateModel");
define('TEMPLATE_CONTAINER', "templateContainer");
define('TEMPLATE_ELEMENT', "templateElement");
define('TEMPLATE_TAG', "templateTag");
define('TEMPLATE_PAGE', "templatePage");
define('TEMPLATE_PAGE_TAG', "templatePageTag");
define('TEMPLATE_PROPERTY_SET', "templatePropertySet");
define('TEMPLATE_PROPERTY', "templateProperty");

// The dummy image used for the editing of the css properties
$gStylingImage = $gCommonImagesUrl . '/' . $gImageStyling;

// The session variable
define('TEMPLATE_SESSION_MODEL', "admin_template_model");
define('TEMPLATE_SESSION_ELEMENT', "admin_template_element");
define('TEMPLATE_SESSION_REQUESTED_URL', "admin_template_requested_url");
define('TEMPLATE_SESSION_PHONE_CLIENT', "admin_template_phone_client");
define('TEMPLATE_SESSION_TOUCH_CLIENT', "admin_template_touch_client");

?>
