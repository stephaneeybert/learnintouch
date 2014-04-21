<?PHP

// Delay before redirecting the current html page
$gRedirectDelay = 4;

$gJSNoStatus = LibJavaScript::getNoStatus();

// The width of content, like a video, on a smartphone
define('TEMPLATE_PHONE_CONTENT_WIDTH', '90%');

// The session variable
define('UTILS_SESSION_RANDOM_SECURITY_CODE', "admin_utils_random_security_code");
define('UTILS_SESSION_PARENT_URL', "admin_utils_parent_url");

// The web socket secret key
define('UTILS_WEB_SOCKET_SECRET_KEY', 'nepasembettermasocket');

?>
