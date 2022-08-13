<?PHP

// The website specific data

// The name of the web site
$gSetupWebsiteName = getenv("WWW_EUROPASPRAK_NAME");

// The domain name
$gSetupWebsiteScheme = getenv("WWW_EUROPASPRAK_SCHEME");
$gSetupWebsiteDomain = getenv("WWW_EUROPASPRAK_DOMAIN");
$gSetupWebsitePort = isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : '';

// The name of the web site url
$gSetupWebsiteUrl = $gSetupWebsiteScheme . '://' . $gSetupWebsiteDomain . ':' . $gSetupWebsitePort;

// The root path
$gRootPath = '/home/europasprak/dev/learnintouch/www.europasprak/';

// The database for one website
define('DB_HOST', getenv("DB_HOST"));
define('DB_PORT', getenv("DB_PORT"));
define('DB_NAME', getenv("WWW_EUROPASPRAK_DB_NAME"));
define('DB_USER', getenv("WWW_EUROPASPRAK_DB_USER"));
define('DB_PASS', getenv("WWW_EUROPASPRAK_DB_PASSWORD"));

?>
