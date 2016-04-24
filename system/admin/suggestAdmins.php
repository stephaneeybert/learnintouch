<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

if ($admins = $adminUtils->selectLikePattern($typedInString)) {
  foreach ($admins as $admin) {
    $adminId = $admin->getId();
    $adminName = $admin->getFirstname() . ' ' . $admin->getLastname();
    // The variable must be html decoded to be correctly displayed
    $adminName = LibString::decodeHtmlspecialchars($adminName);
    $adminName = LibString::escapeDoubleQuotes($adminName);
    $responseText .= " {\"id\": \"$adminId\", \"label\": \"$adminName\", \"value\": \"$adminName\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
