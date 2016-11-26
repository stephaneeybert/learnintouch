<?PHP

// Include some properties
require_once("properties.php");

// Get the environment variables
$HOSTNAME = $_SERVER["SERVER_NAME"];
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
$PHP_SELF = $_SERVER["PHP_SELF"];
$SCRIPT_FILENAME = $_SERVER["SCRIPT_FILENAME"];

$HTTP_HOST = $_SERVER["HTTP_HOST"];
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $HTTP_USER_AGENT = $_SERVER["HTTP_USER_AGENT"]; 
} else {
  $HTTP_USER_AGENT = '';
}
$REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
$REQUEST_URI = $_SERVER["REQUEST_URI"];

date_default_timezone_set("Europe/London");

function isLocalhost() {
  global $gSetupWebsiteUrl;

  if (strstr($gSetupWebsiteUrl, "localhost") || strstr($gSetupWebsiteUrl, "dev.")) {
    return(true);
  } else {
    return(false);
  }
}

if (isLocalhost()) {
  $gHostname = LOCALHOST;
  $gSocketHostname = NODEJS_SOCKET_LOCALHOST;
} else {
  $gHostname = $gSetupWebsiteUrl;
  $gSocketHostname = $gHostname;
}

// Include the paths to the engine modules
require_once("path.php");

// Include the paths to the library
require_once("library.php");

// Include the services
require_once("includes.php");

// Include the database setup file
require_once("database.php");

// Instantiate the services
require_once("services.php");

// Inject the dependencies
require_once("inject.php");

// Init
require_once("init.php");

?>
