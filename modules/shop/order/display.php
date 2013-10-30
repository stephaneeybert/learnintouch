<?PHP

require_once("website.php");

// Check that the user can access this page
// The user may access this page without being logged in if a unique token is used
// This allows a user to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // In case the website email is also the one of a registered user then log in the user
  $email = LibEnv::getEnvHttpGET("email");
  if ($user = $userUtils->selectByEmail($email)) {
    $userUtils->openUserSession($email);
  }
} else {
  // If no token is used, then
  // check that the user is logged in
  $userUtils->checkUserLogin();
}

$shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");

if (!$shopOrderId) {
  $str = LibHtml::urlRedirect("$gShopUrl/order/display_list.php");
  printContent($str);
  exit;
}


$str = $shopOrderUtils->render($shopOrderId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
