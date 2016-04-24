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

if ($elearningClasses = $elearningClassUtils->selectLikePattern($typedInString)) {
  foreach ($elearningClasses as $elearningClass) {
    $elearningClassId = $elearningClass->getId();
    $name = $elearningClass->getName();
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $responseText .= " {\"id\": \"$elearningClassId\", \"label\": \"$name\", \"value\": \"$name\"},";
    }
  }

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
